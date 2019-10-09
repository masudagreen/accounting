<?php /* Smarty version 3.1.24, created on 2016-08-18 12:50:23
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/tree.js" */ ?>
<?php
/*%%SmartyHeaderCode:196288296257b5af0f1f23b8_79299424%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '36e988d7ee951b6223c6c4d9ea69b61053cd2af1' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/tree.js',
      1 => 1471523676,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '196288296257b5af0f1f23b8_79299424',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_57b5af0f32c553_71084733',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_57b5af0f32c553_71084733')) {
function content_57b5af0f32c553_71084733 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '196288296257b5af0f1f23b8_79299424';
?>

/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_Tree = Class.create(Code_Lib_ExtLib,
{

	/**
	 *
	*/
	_staticWhole : { numLength : 25 },
	initialize : function(obj)
	{
		this._extAllot(obj);
		this._iniVars(obj);
		this._iniListener();
		this._iniWrap();
		this._iniFormat();
		this._iniBar();
		this._iniLine();
		this._iniFind();
		this._iniBtn();
		this._iniPage();
		this._iniBtnBottom();
	},

	/**
	 *
	*/
	iniReload : function()
	{
		this._resetEditVars();
		this._resetFindVars();
		this.stopListener();
		this.removeWrap();
		this._updateWrapStyle();
		this._updateFormatStyle();
		this._iniBar();
		this._iniLine();
		this._updateFindWidth();
		this._iniBtn();
		this._iniPage();
		this._iniBtnBottom();
		this.setScroll();
	},

	/**
	 *
	*/
	iniReloadFind : function()
	{
		this._resetEditVars();
		if (!this._checkFind()) {
			this.iniReload();
			return;
		}
		this.stopListener();
		this.removeWrap();
		this._iniBar();
		this._iniFindTemplate();
	},

	/**
	 *
	*/
	_iniVars : function(obj)
	{
		this._extVars(obj);
		this._resetEditVars();
		this._iniCakeBar();
		this.allot({
			insCurrent : this.insCurrent,
			from       : '_iniVars'
		});
	},

	/**
	 *
	*/
	_iniWrap : function()
	{
		this._extWrap();
		this.eleWrap.style.width = this._getWrapWidth() + 'px';
		this.eleWrap.style.height = this._getWrapHeight() + 'px';
	},

	/**
	 *
	*/
	_updateWrapStyle : function()
	{

		this.eleWrap.style.width = this._getWrapWidth() + 'px';
		this.eleWrap.style.height = this._getWrapHeight() + 'px';
	},

	/**
	 *
	*/
	removeWrap : function()
	{
		if (this.insFormat.eleTemplate.header) {
			if (this.insFormat.eleTemplate.header.down('.codeLibBaseMarginLeftFive', 0)) {
				this.insFormat.eleTemplate.header.down('.codeLibBaseMarginLeftFive', 0).innerHTML = '';
			}
			if (this.insFormat.eleTemplate.header.down('.codeLibBaseMarginRightFive', 0)) {
				this.insFormat.eleTemplate.header.down('.codeLibBaseMarginRightFive', 0).innerHTML = '';
			}
		}
		this.insFormat.eleTemplate.body.innerHTML = '';
		if (!this.vars.varsStatus.flagInnerFindUse) {
			if (this.vars.varsStatus.flagInnerPageUse) {
				if (this.insFormat.eleTemplate.fooder.down('.codeLibBaseMarginLeftFive', 0)) {
					this.insFormat.eleTemplate.fooder.down('.codeLibBaseMarginLeftFive', 0).innerHTML = '';
				}
				if (this.insFormat.eleTemplate.fooder.down('.codeLibBaseMarginRightFive', 0)) {
					this.insFormat.eleTemplate.fooder.down('.codeLibBaseMarginRightFive', 0).innerHTML = '';
				}
			}
			if (!this.vars.varsStatus.flagFindUse) {
				if (this.eleInsertBtnLeft) this.eleInsertBtnLeft.innerHTML = '';
				if (this.eleInsertBtnRight) this.eleInsertBtnRight.innerHTML = '';
			}
		}
	},


	/**
	 *
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
	 *
	*/
	insFormat : null,
	_iniFormat : function()
	{
		this._extFormat();
	},


	/**
	 *
	*/
	_varsWidth : {numTree : 0, numFind : 0},
	_staticWidth : {numBar : 17, numIdle : 16, numBlock : 16, numMargin : 5},
	_setWidth : function()
	{
		var array = this.insFormat.eleTemplate.body.style.width.split('px');
		var width = parseFloat(array[0]);
		this._varsWidth.numTree = width - this._staticWidth.numMargin
									- this._staticWidth.numBar;
		this._getWidthTree({arr : this.vars.varsDetail});
		this.eleTree.setStyle({
			width : this._varsWidth.numTree + 'px'
		});
	},

	/**
	 *
	*/
	_getWidthTree : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].flagUse) continue;
			var array = (obj.arr[i].id).split(this.idSelf);
			var level = array[1];
			var arrayLevel = level.split('-');
			var num = (arrayLevel.length) - 1;
			num++;
			if (obj.arr[i].flagCheckUse && this.vars.varsStatus.flagCheckUse) {
				num++;
				numMargin++;
			}
			if (obj.arr[i].flagRemoveUse
				&& this.vars.varsStatus.flagRemoveUse
				&& this.vars.varsStatus.flagRemoveNow
			) {
				num++;
			}
			if (obj.arr[i].flagEditUse
				&& this.vars.varsStatus.flagEditUse
				&& this.vars.varsStatus.flagEditNow
			) {
				num++;
			}
			var numMargin = 1;
			var numWidth = numMargin * this._staticWidth.numMargin
					+ num * this._staticWidth.numBlock
					+ $(obj.arr[i].id).down('.codeLibTreeTitle', 0).offsetWidth
					+ this._staticWidth.numIdle
					+ this._staticWidth.numBar;
			if ( numWidth > this._varsWidth.numTree) {
				this._varsWidth.numTree = numWidth;
			}
			this._getWidthTree({arr:obj.arr[i].child});
		}
	},

	/**
	 *
	*/
	_setWidthFind : function()
	{
		this._varsWidth.numFind = $(this.eleInsert).offsetWidth - this._staticWidth.numMargin;
		this._getWidthFind({arr:this.vars.varsDetail});
		this.eleTree.setStyle({
			width : this._varsWidth.numFind + 'px'
		});
	},

	/**
	 *
	*/
	_getWidthFind : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].flagUse) continue;
			if ($(obj.arr[i].id)) {
				var num = 1;
				if (obj.arr[i].flagRemoveUse
					&& this.vars.varsStatus.flagRemoveUse
					&& this.vars.varsStatus.flagRemoveNow
				) {
					num++;
				}
				if (obj.arr[i].flagEditUse
					&& this.vars.varsStatus.flagEditUse
					&& this.vars.varsStatus.flagEditNow
				) {
					num++;
				}
				var numMargin = 1;
				var numWidth = numMargin * this._staticWidth.numMargin
						+ num * this._staticWidth.numBlock
						+ $(obj.arr[i].id).down('.codeLibTreeTitle', 0).offsetWidth
						+ this._staticWidth.numIdle
						+ this._staticWidth.numBar;
				if ( numWidth > this._varsWidth.numFind) {
					this._varsWidth.numFind = numWidth;
				}
			}
			this._getWidthFind({arr:obj.arr[i].child});
		}
	},

	/**
	 *
	*/
	_mousedownBarLink : function(evt, obj)
	{
		evt.stop();
		this.allot({
			insCurrent : this.insCurrent,
			from       : '_mousedownBarLink'
		});
	},

	/**
	 *
	*/
	_mousedownBarAdd : function(evt, obj)
	{
		evt.stop();
		this.allot({
			insCurrent : this.insCurrent,
			from       : '_mousedownBarAdd'
		});
	},

	/**
	 *
	*/
	_mousedownEdit : function(evt, obj)
	{
		evt.stop();
		if (this._varsEdit.flag) return;
		this._varsEdit.flag = 1;
		this._setEdit({vars : obj.vars});
		this._setEditListener({vars : obj.vars});
		this.allot({
			insCurrent : this.insCurrent,
			from       : '_mousedownEdit',
			vars       : obj.vars
		});
	},

	/**
	 *
	*/
	_varsEdit : {flag : 0},
	_staticEdit : {numMargin : 5},
	_setEdit : function(obj)
	{
		var ele = $(obj.vars.id);
		ele.removeClassName('unselect');
		var ele = $(obj.vars.id).down('.codeLibTreeTitle', 0);
		var numWidth = ele.offsetWidth;
		ele.hide();
		var ele = $(obj.vars.id).down('.codeLibTreeForm', 0);
		ele.show();
		var ele = $(obj.vars.id).down('.codeLibTreeFormTag', 0);
		ele.setStyle({
			width : (this._staticEdit.numMargin + numWidth) + 'px'
		});
	},

	/**
	 *
	*/
	_resetEditVars : function()
	{
		this._varsEdit.flag = 0;
	},

	/**
	 *
	*/
	_setEditListener : function(obj)
	{
		var ele = $(obj.vars.id).down('.codeLibTreeFormTag', 0);
		ele.focus();
		if (this.vars.varsStatus.flagLockUse && this.vars.varsStatus.flagLockNow) return;
		this.insListener.set({
			flagType15 : 1, bindAsEvent : 1, insCurrent : this, event : 'blur',
			strFunc : '_blurEdit', ele : ele, vars : { vars : obj.vars }
		});
	},

	/**
	 *
	*/
	_blurEdit : function(obj, evt) {
		if (obj) evt.stop();
		else obj = evt;
		var ele = $(obj.vars.id).down('.codeLibTreeFormTag', 0);
		obj.vars.strTitle = this._escapeEditValue({value : ele.value});
		this.iniReload();
		this.allot({
			from       : '_blurEdit',
			insCurrent : this.insCurrent
		});
	},

	/**
	 *
	*/
	_escapeEditValue : function(obj)
	{
		this.insEscape = new Code_Lib_Escape();
		obj.value = this.insEscape.get({data : obj.value, flagType : 'fromTag'});

		return obj.value;
	},

	/**
	 * Remove
	*/
	_mousedownRemove : function(evt, obj)
	{
		evt.stop();
		this._varsMove.numBlock = {};
		this._getBlock({
			arr      : this.vars.varsDetail,
			idTarget : obj.vars.id
		});
		this.vars.varsDetail = this._removeBlock({
			arr    : this.vars.varsDetail,
			idTarget : obj.vars.id
		});
		this.vars.varsDetail = this.getBlockLevel();
		this._varsMove = {};
		var flag = this.allot({
			insCurrent : this.insCurrent,
			from       : '_mousedownRemove',
			vars       : obj.vars
		});
		if (flag) this.iniReload();
	},

	/**
	 * Block
	*/
	_getBlock : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == obj.idTarget) {
				var objTemp = {};
				objTemp = obj.arr[i];
				if (obj.arr[i].child.length) objTemp.child = this._getBlockChild({arr : obj.arr[i].child});
				else objTemp.child = [];
				this._varsMove.numBlock = objTemp;
			}
			else this._getBlock({arr : obj.arr[i].child, idTarget : obj.idTarget});
		}
	},

	/**
	 *
	*/
	_getBlockChild : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			this._getBlockChild({arr : obj.arr[i].child});
		}

		return obj.arr;
	},

	/**
	 *
	*/
	_removeBlock : function(obj)
	{
		var numTop = -1;
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == obj.idTarget) {
				numTop = i;
			}
			var num = this._checkBlockArrayNum({arr:obj.arr[i].child,idTarget : obj.idTarget});
			if (num != -1) {
				obj.arr[i].child =
					obj.arr[i].child.slice(0, num).concat(obj.arr[i].child.slice((num + 1), obj.arr[i].child.length));
			}
			this._removeBlock({arr : obj.arr[i].child, idTarget : obj.idTarget});
		}
		if (numTop != -1) {
			obj.arr = obj.arr.slice(0, numTop).concat(obj.arr.slice((numTop + 1), obj.arr.length));
		}

		return obj.arr;
	},

	/**
	 *
	*/
	_checkBlockArrayNum : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == obj.idTarget) return i;
		}

		return -1;
	},

	/**
	 *
	*/
	_setBlockInsert : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == obj.idTarget) {
				obj.arr[i].child.push(obj.numBlock);
				break;
			}
			this._setBlockInsert({
				arr      : obj.arr[i].child,
				idTarget : obj.idTarget,
				numBlock : obj.numBlock
			});
		}

		return obj.arr;
	},

	/**
	 *
	*/
	_setBlockInsertInside : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == obj.idTarget) {
				obj.arr[i].varsInside.unshift(obj.numBlock);
				break;
			}
			this._setBlockInsertInside({
				arr      : obj.arr[i].child,
				idTarget : obj.idTarget,
				numBlock : obj.numBlock
			});
		}

		return obj.arr;
	},

	/**
	 *
	*/
	_setBlockAfter : function(obj)
	{
		var numTop = -1;
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == obj.idTarget) {
				numTop = i;
			}
			var num = this._checkBlockArrayNum({
				arr      : obj.arr[i].child,
				idTarget : obj.idTarget
			});
			var array = [obj.numBlock];
			if (num != -1) {
				obj.arr[i].child =
					obj.arr[i].child.slice(0, num + 1).concat(
						array.concat(obj.arr[i].child.slice((num + 1),
						obj.arr[i].child.length))
					);
			}
			this._setBlockAfter({
				arr      : obj.arr[i].child,
				idTarget : obj.idTarget,
				numBlock : obj.numBlock
			});
		}
		if (numTop != -1) {
			var array = [obj.numBlock];
			obj.arr = obj.arr.slice(0, numTop + 1).concat(array.concat(obj.arr.slice((numTop + 1), obj.arr.length)));
		}

		return obj.arr;
	},

	/**
	 *
	*/
	_varsBlockFoldNow : null,
	getBlockLevel : function()
	{
		return this._setBlockLevel({
			arr    : this.vars.varsDetail,
			parent : this.idSelf
		});

	},

	/**
	 *
	*/
	_setBlockLevel : function(obj)
	{
		this._varsBlockFoldNow = {};
		this._setBlockLevelFoldNow(obj);
		this._setBlockLevelId(obj);
		this.setCake({arr : obj.arr});

		return obj.arr;
	},

	/**
	 *
	*/
	_setBlockLevelId : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			var temp = obj.parent + '-' + i;
			if (this._varsBlockFoldNow[temp]) obj.arr[i].flagFoldNow = 1;
			obj.arr[i].id = temp;
			this._setBlockLevelId({
				arr    : obj.arr[i].child,
				parent : temp
			});
		}
	},

	/**
	 *
	*/
	_setBlockLevelFoldNow : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].flagFoldUse) {
				if (obj.arr[i].flagFoldNow) this._varsBlockFoldNow[obj.arr[i].id] = 1;
			}
			this._setBlockLevelFoldNow({
				arr    : obj.arr[i].child
			});
		}
	},

	/**
	 *
	*/
	updateBlockVars : function(obj)
	{
		this._updateBlockVars({
			arr  : this.vars.varsDetail,
			vars : obj.vars
		});
	},

	/**
	 *
	*/
	_updateBlockVars : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if ( obj.vars.vars.idTarget == obj.arr[i].vars.idTarget ) {
				obj.vars.id = obj.arr[i].id;
				obj.arr[i] = obj.vars;
				return;
			}
			if (obj.arr[i].child.length) {
				this._updateBlockVars({
					arr  : obj.arr[i].child,
					vars : obj.vars,
				});
			}
		}
	},

	/**
	 * Move
	*/
	eventMove : function(obj)
	{
		if (!this.vars.varsStatus.flagMoveUse) return;
		this._setMoveWrapListener();
		this._setMoveListener();
		var topMin = $(this.idSelf).up('.codeLibWindow', 0).offsetTop
					+ this.insFormat.eleTemplate.body.offsetTop
					+ this._staticMove.numAreaNone;
		var topMax = topMin
					+ this.insFormat.eleTemplate.body.offsetHeight
					- this._staticMove.numArea * 2;
		var leftMin = $(this.idSelf).up('.codeLibWindow', 0).offsetLeft
					+ this.insFormat.eleTemplate.body.offsetLeft
					+ this._staticMove.numAreaNone;
		var leftMax = leftMin
					+ this.insFormat.eleTemplate.body.offsetWidth
					- this._staticMove.numArea * 2;
		this._varsMove = {};
 		var array = (this.idSelf + '-').split(this.idSelf);
		var level = array[1];
		this._varsMove = {
			flag          : 1,
			flagEventMove : (obj.flag)? obj.flag : 1,
			flagMove      : 0,

			vars : obj.vars,

			arrayLevel : level.split('-'),

			mouseupId         : '',
			mouseupAction     : '',
			mouseupWrapAction : '',

			numNaviLeft : 0,
			numNaviTop  : 0,
			naviEle     : null,

			leftMax : leftMax,
			leftMin : leftMin,
			topMax  : topMax,
			topMin  : topMin,

			timerFlag : 0,
			timerVars : null,

			numBlock           : {}
		};
		var zIndex = this.insRoot.getZIndex();
		var ele = $(document.createElement('ele'));
		ele.removeClassName('codeLibTreeBtnOver');
		$(this.insRoot.vars.varsSystem.id.root).insert(ele);
		this._varsMove.naviEle = ele;
		this._removeImgStyleCursor({arr : this.vars.varsDetail});
	},

	/**
	 *
	*/
	_mouseoverMoveWrap : function()
	{
		if (!this.vars.varsStatus.flagMoveUse) return;
		if (this._varsMove.flag) {
			this._varsMove.mouseupWrapId = 'dummy';
			this._varsMove.mouseupWrapAction = 'add';
		}
	},

	/**
	 *
	*/
	_mouseoutMoveWrap : function()
	{
		if (!this.vars.varsStatus.flagMoveUse) return;
		if (this._varsMove.flag) {
			this._varsMove.mouseupWrapId = '';
			this._varsMove.mouseupWrapAction = '';
		}
	},

	/**
	 *
	*/
	_setMoveWrapListener : function(obj)
	{
		if (this.vars.varsStatus.flagLockUse && this.vars.varsStatus.flagLockNow) return;
		this.insListener.set({
			bindAsEvent : 0, insCurrent : this, event : 'mouseover',
			strFunc : '_mouseoverMoveWrap', ele : this.insFormat.eleTemplate.body,
			vars : ''
		});
		this.insListener.set({
			bindAsEvent : 0, insCurrent : this, event : 'mouseout',
			strFunc : '_mouseoutMoveWrap', ele : this.insFormat.eleTemplate.body,
			vars : ''
		});
	},

	/**
	 *
	*/
	_setMoveListener : function(obj)
	{
		if (this.vars.varsStatus.flagLockUse && this.vars.varsStatus.flagLockNow) return;
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
	_mousedownMove : function(evt, obj)
	{
		if (!this.vars.varsStatus.flagMoveUse) return;
		this._setMoveListener();
		var topMin = $(this.idSelf).up('.codeLibWindow', 0).offsetTop
					+ this.insFormat.eleTemplate.body.offsetTop
					+ this._staticMove.numAreaNone;
		var topMax = topMin
					+ this.insFormat.eleTemplate.body.offsetHeight
					- this._staticMove.numArea * 2;
		var leftMin = $(this.idSelf).up('.codeLibWindow', 0).offsetLeft
					+ this.insFormat.eleTemplate.body.offsetLeft
					+ this._staticMove.numAreaNone;
		var leftMax = leftMin
					+ this.insFormat.eleTemplate.body.offsetWidth
					- this._staticMove.numArea * 2;
		this._varsMove = {};
		var array = (obj.vars.id).split(this.idSelf);
		var level = array[1];
		this._mouseoutBtn({vars : obj.vars});
		this._varsMove = {
			flag : 1,
			ele  : evt.element(),
			vars : obj.vars,

			arrayLevel : level.split('-'),

			mouseupId     : '',
			mouseupAction : '',
			numNaviLeft   : evt.pointerX(),
			numNaviTop    : evt.pointerY(),
			naviEle       : null,

			leftMax : leftMax,
			leftMin : leftMin,
			topMax  : topMax,
			topMin  : topMin,

			timerFlag : 0,
			timerVars : null,

			numBlock           : {}
		};
		this._setMoveNavi({
			vars : obj.vars,
			evt  : evt
		});
		this._removeImgStyleCursor({arr : this.vars.varsDetail});
		this.allot({
			insCurrent : this.insCurrent,
			from       : '_mousedownMove',
			vars       : obj.vars
		});
		evt.stop();
	},

	/**
	 *{
		numLeftMax : 0,
		numLeftMin : 0,
		numTopMax  : 0,
		numTopMin  : 0
	 *}
	*/
	modifyVarsMove : function(obj)
	{
		if (this._varsMove.flag) {
			if (obj.numLeftMax != undefined) {
				this._varsMove.leftMax += obj.numLeftMax;
			}
			if (obj.numLeftMin != undefined) {
				this._varsMove.leftMin += obj.numLeftMin;
			}
			if (obj.numTopMax != undefined) {
				this._varsMove.topMax += obj.numTopMax;
			}
			if (obj.numTopMin != undefined) {
				this._varsMove.topMin += obj.numTopMin;
			}
		}
	},

	/**
	 *
	*/
	_staticMove : {
		numArea : 25, numAreaNone : 16, numNaviLeft : 15, numNaviTop : 5, numPosShift : 5, numTimer : 2000
	},
	_mousemoveMove : function(evt, obj)
	{
		if (obj) evt.stop();
		else obj = evt;
		if (!this.vars.varsStatus.flagMoveUse || !this._varsMove.flag) return;
		if (this._varsMove.flagEventMove && !this._varsMove.flagMove) {
			if (evt.pointerY() > this._varsMove.topMin
				&& evt.pointerY() < this._varsMove.topMax
				&& evt.pointerX() > this._varsMove.leftMin
				&& evt.pointerX() < this._varsMove.leftMax
			) {
				this._varsMove.flagMove = 1;
			}
		} else {
			if (evt.pointerY() < this._varsMove.topMin) {
				this.insFormat.eleTemplate.body.scrollTop -= this._staticMove.numPosShift;
			} else if (evt.pointerY() > this._varsMove.topMax) {
				this.insFormat.eleTemplate.body.scrollTop += this._staticMove.numPosShift;
			}
			if (evt.pointerX() < this._varsMove.leftMin) {
				this.insFormat.eleTemplate.body.scrollLeft -= this._staticMove.numPosShift;
			} else if (evt.pointerX() > this._varsMove.leftMax) {
				this.insFormat.eleTemplate.body.scrollLeft += this._staticMove.numPosShift;
			}
		}

		this._varsMove.naviEle.setStyle({
			left : (evt.pointerX() + this._staticMove.numNaviLeft) + 'px',
			top  : (evt.pointerY() + this._staticMove.numNaviTop) + 'px'
		});
	},

	/**
	 *
	*/
	_mouseupMove : function(evt)
	{
		if (!this.vars.varsStatus.flagMoveUse || !this._varsMove.flag) return;
		if (this._varsMove.flagEventMove == 1) {
			if (this._varsMove.mouseupId) {
				this._varsMove.numBlock = this._varsMove.vars;
				if (this._varsMove.mouseupAction == 'insert') {
					this.vars.varsDetail = this._setBlockInsert({
						arr      : this.vars.varsDetail,
						idTarget : this._varsMove.mouseupId,
						numBlock : this._varsMove.numBlock
					});


				} else if (this._varsMove.mouseupAction == 'after') {

					this.vars.varsDetail = this._setBlockAfter({
						arr      : this.vars.varsDetail,
						idTarget : this._varsMove.mouseupId,
						numBlock : this._varsMove.numBlock
					});


				}
				this.vars.varsDetail = this.getBlockLevel();
				this.allot({
					insCurrent : this.insCurrent,
					from       : '_mouseupMoveEventMove',
					vars       : this.vars.varsDetail
				});

			} else if (this._varsMove.mouseupWrapId) {
				this._varsMove.numBlock = this._varsMove.vars;
				if (this._varsMove.mouseupWrapAction == 'add') {
					this.vars.varsDetail.unshift(this._varsMove.numBlock);

				}
				this.vars.varsDetail = this.getBlockLevel();
				this.allot({
					insCurrent : this.insCurrent,
					from       : '_mouseupMoveEventMove',
					vars       : this.vars.varsDetail
				});

			}
			this._setImgStyleCursor({arr : this.vars.varsDetail});

		} else if (this._varsMove.flagEventMove == 'varsInside') {
			if (this._varsMove.mouseupId) {
				this._varsMove.numBlock = this._varsMove.vars;
				var flag = 0;
				if (this._varsMove.mouseupAction == 'insert') {
					if (this._varsMove.vars.idTarget) {
						flag = this.allot({
							insCurrent : this.insCurrent,
							from       : '_preMouseupMoveEventMove',
							idTarget   : this._varsMove.vars.idTarget
						});
					}
					if (!flag) {
						this.vars.varsDetail = this._setBlockInsert({
							arr      : this.vars.varsDetail,
							idTarget : this._varsMove.mouseupId,
							numBlock : this._varsMove.numBlock
						});
					}

				} else if (this._varsMove.mouseupAction == 'after') {
					if (this._varsMove.vars.idTarget) {
						flag = this.allot({
							insCurrent : this.insCurrent,
							from       : '_preMouseupMoveEventMove',
							idTarget   : this._varsMove.vars.idTarget
						});
					}
					if (!flag) {
						this.vars.varsDetail = this._setBlockAfter({
							arr      : this.vars.varsDetail,
							idTarget : this._varsMove.mouseupId,
							numBlock : this._varsMove.numBlock
						});
					}

				}
				this.vars.varsDetail = this.getBlockLevel();
				this.allot({
					insCurrent : this.insCurrent,
					flag       : flag,
					from       : '_mouseupMoveEventMove',
					vars       : this.vars.varsDetail
				});

			} else if (this._varsMove.mouseupWrapId) {
				this._varsMove.numBlock = this._varsMove.vars;
				var flag = 0;
				if (this._varsMove.mouseupWrapAction == 'add') {
					if (this._varsMove.vars.idTarget) {
						flag = this.allot({
							insCurrent : this.insCurrent,
							from       : '_preMouseupMoveEventMove',
							idTarget   : this._varsMove.vars.idTarget
						});
					}
					if (!flag) {
						this.vars.varsDetail[0].child.unshift(this._varsMove.numBlock);
					}

				}
				this.vars.varsDetail = this.getBlockLevel();
				this.allot({
					insCurrent : this.insCurrent,
					flag       : flag,
					from       : '_mouseupMoveEventMove',
					vars       : this.vars.varsDetail
				});

			}
			this._setImgStyleCursor({arr : this.vars.varsDetail});

		} else if (this._varsMove.flagEventMove == 'varsOutside') {
			if (this._varsMove.mouseupId) {
				var flag = 0;
				if (this._varsMove.mouseupAction == 'insert') {

					this._getBlock({
						arr      : this.vars.varsDetail,
						idTarget : this._varsMove.mouseupId
					});

					if (this._varsMove.vars.idTarget) {
						flag = this.allot({
							insCurrent : this.insCurrent,
							from       : '_preMouseupMoveEventMove',
							idTarget   : this._varsMove.vars.idTarget,
							arr        : this._varsMove.numBlock.varsInside
						});
					}

					if (!flag) {
						this.vars.varsDetail = this._setBlockInsertInside({
							arr      : this.vars.varsDetail,
							idTarget : this._varsMove.mouseupId,
							numBlock : this._varsMove.vars
						});
					}
				}
				this.vars.varsDetail = this.getBlockLevel();
				this.allot({
					insCurrent : this.insCurrent,
					from       : '_mouseupMoveEventMove',
					flag       : flag,
					vars       : this.vars.varsDetail
				});

			}
			this._setImgStyleCursor({arr : this.vars.varsDetail});

		} else {
			if (this._varsMove.mouseupId) {
				this._varsMove.numBlock = {};
				var varsDetail = (Object.toJSON(this.vars.varsDetail)).evalJSON();
				this._getBlock({
					arr      : varsDetail,
					idTarget : this._varsMove.vars.id
				});
				varsDetail = this._removeBlock({
					arr      : varsDetail,
					idTarget : this._varsMove.vars.id
				});
				if (this._varsMove.mouseupAction == 'insert') {
					varsDetail = this._setBlockInsert({
						arr      : varsDetail,
						idTarget : this._varsMove.mouseupId,
						numBlock : this._varsMove.numBlock
					});

				} else if (this._varsMove.mouseupAction == 'after') {
					varsDetail = this._setBlockAfter({
						arr      : varsDetail,
						idTarget : this._varsMove.mouseupId,
						numBlock : this._varsMove.numBlock
					});
				}

				varsDetail = this._setBlockLevel({
					arr    : varsDetail,
					parent : this.idSelf
				});

				var flag = this.allot({
					insCurrent : this.insCurrent,
					from       : '_mouseupMove',
					vars       : varsDetail
				});
				if (!flag) {
					this.vars.varsDetail = varsDetail;
					this.iniReload();
				}

			} else {
				this._setImgStyleCursor({arr : this.vars.varsDetail});

			}
			var data = this._varsMove.vars;
			this._mouseoutBtn({vars : data});
		}

		this._varsMove.naviEle.remove();
		this._varsMove = {};
		evt.stop();
	},

	/**
	 *
	*/
	_checkMoveId : function()
	{

	},

	/**
	 *
	*/
	_setMoveNavi : function(obj)
	{
		var zIndex = this.insRoot.setZIndex();
		var ele = $(obj.vars.id).cloneNode(true);
		ele.removeClassName('codeLibTreeBtnOver');
		$(this.insRoot.vars.varsSystem.id.root).insert(ele);
		this._varsMove.naviEle = ele;
		ele.addClassName('codeLibTreeNavi');
		var array = ele.childNodes;
		for (var j = 0; j < array.length; j++) {
			array[j].removeClassName('codeLibTreeBlock');
		}
		ele.setStyle({
			left   : (obj.evt.pointerX() + this._staticMove.numNaviLeft) + 'px',
			top    : (obj.evt.pointerY() + this._staticMove.numNaviTop) + 'px',
			zIndex : zIndex
		});
	},

	/**
	 *
	*/
	varsLineSelect : {id : null},
	_iniFind : function()
	{
		if (!this.vars.varsStatus.flagFindUse) return;
		this._setFind();
	},

	/**
	 *
	*/
	insFind : null,
	_setFind : function()
	{
		this.insFind = new Code_Lib_Btn();
		this.insFind.iniBtnSearch({
			eleInsert  : (this.vars.varsStatus.flagInnerFindUse)?
					  this.insFormat.eleTemplate.fooder.down('.codeLibBaseMarginLeftFive', 0)
					: this.eleInsertBtnLeft,
			strFunc    : '_mousedownFind',
			insCurrent : this,
			id         : this.idSelf + 'Find',
			numWidth   : this._getFindWidth({
				numWidth : this.vars.varsFind.numWidth
			}),
			unitWidth  : 'px',
			strTitle   : this.vars.varsFind.strTitle
		});
	},

	/**
	 *
	*/
	_resetFindVars : function()
	{
		if(!this.vars.varsStatus.flagFindUse) return;
		this.vars.varsFind.num = 0;
	},

	/**
	 *
	*/
	_getFindWidth : function(obj)
	{
		var array = this.eleInsert.style.width.split('px');
		var data =  Math.floor(parseFloat(array[0]) * obj.numWidth / 100);

		return  data;
	},

	/**
	 *
	*/
	_mousedownFind : function(obj)
	{
		if (this.vars.varsStatus.flagLockUse && this.vars.varsStatus.flagLockNow) return;
		if (obj.value == '') {
			this.vars.varsFind.flag = 0;
			this.vars.varsFind.value = '';
		} else {
			this.vars.varsFind.flag = 1;
			this.vars.varsFind.value = obj.value;
		}
		this.iniReloadFind();
	},

	/**
	 *
	*/
	_updateFindWidth : function()
	{
		if (!this.vars.varsStatus.flagFindUse) return;
		$(this.idSelf + 'Find').down('.codeLibBtnSearchInput', 0).style.width = this._getFindWidth({
			numWidth : this.vars.varsFind.numWidth
		}) + 'px';
	},

	/**
	 *
	*/
	_checkFind : function()
	{
		this._resetFindVars();
		this._checkFindList({
			idParent : this.idSelf ,
			arr      : this.vars.varsDetail
		});

		return this.vars.varsFind.num;
	},

	/**
	 *
	*/
	_setFindMatch : function(obj)
	{
		var cut = obj.vars;
		if (!this.vars.varsFind.flag) return;
		var flag = cut.strTitle.match(new RegExp( this.vars.varsFind.value ,'im'));

		return flag;
	},

	/**
	 *
	*/
	_checkFindList : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].flagUse) continue;
			obj.arr[i].id = obj.idParent + '-' + i;
			if (this.vars.varsStatus.flagBtnUse && !obj.arr[i].flagBtnUse) {
				if (obj.arr[i].child.length) {
					this._checkFindList({
						arr      : obj.arr[i].child,
						idParent : obj.arr[i].id
					});
				}
				continue;
			}
			var flag = this._setFindMatch({vars : obj.arr[i]});
			if (!flag) {
				if (obj.arr[i].child.length) {
					this._checkFindList({
						arr      : obj.arr[i].child,
						idParent : obj.arr[i].id
					});
				}
			} else {
				this.vars.varsFind.num++;
				if (obj.arr[i].child.length) {
					this._checkFindList({
						arr      : obj.arr[i].child,
						idParent : obj.arr[i].id
					});
				}
			}
		}
	},

	/**
	 *
	*/
	_iniFindTemplate : function()
	{
		this.eleTree = $(document.createElement('div'));
		this.eleTree.addClassName('codeLibBaseMarginLeftFive');
		$(this.insRoot.vars.varsSystem.id.root).insert(this.eleTree);
		this._setFindTemplate({
			idParent : this.idSelf ,
			arr      : this.vars.varsDetail
		});
		this._setWidthFind();
		this.insFormat.eleTemplate.body.insert(this.eleTree);
	},

	/**
	 *
	*/
	_setFindTemplate : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].flagUse) continue;
			obj.arr[i].id = obj.idParent + '-' + i;
			var array = (obj.arr[i].id).split(this.idSelf);
			var level = array[1];
			var arrayLevel = level.split('-');
			var num = (arrayLevel.length) - 2;
			if (this.vars.varsStatus.flagBtnUse && !obj.arr[i].flagBtnUse) {
				if (obj.arr[i].child.length) {
					this._setFindTemplate({
						arr      : obj.arr[i].child,
						idParent : obj.arr[i].id
					});
				}
				continue;
			}
			var flag = this._setFindMatch({vars : obj.arr[i]});
			if (!flag) {
				if (obj.arr[i].child.length) {
					this._setFindTemplate({
						arr      : obj.arr[i].child,
						idParent : obj.arr[i].id
					});
				}
				continue;
			}

			/*sortLine*/
			var eleSort = $(document.createElement('div'));
			eleSort.addClassName('codeLibTreeSort');
			eleSort.unselectable = 'on';

			/*btnLine*/
			var eleDiv = $(document.createElement('div'));
			eleDiv.unselectable = 'on';
			eleDiv.addClassName('codeLibTreeBtn');
			eleDiv.addClassName('unselect');
			eleDiv.id = obj.arr[i].id;
			var arrChild = obj.arr[i].child;
			var numChild = 0;
			for (var j = 0; j < arrChild.length; j++) {
				if (arrChild[j].flagUse) {
					numChild++;
				}
			}
			var children = ' (' + numChild.length + ')';
			var strInside = '';
			if (obj.arr[i].varsInside) strInside = ' [' + obj.arr[i].varsInside.length + ']';

			/*title*/
			var eleTitle = $(document.createElement('span'));
			eleTitle.unselectable = 'on';
			eleTitle.addClassName('codeLibBaseMarginLeftFive');
			if (obj.arr[i].flagChildrenUse && obj.arr[i].flagInsideUse) eleTitle.title = obj.arr[i].strTitle + children + strInside;
			else if (obj.arr[i].flagInsideUse) eleTitle.title = obj.arr[i].strTitle + strInside;
			else if (obj.arr[i].flagChildrenUse) eleTitle.title = obj.arr[i].strTitle + children;
			else eleTitle.title = obj.arr[i].strTitle;

			if (this.vars.varsStatus.flagLockUse && this.vars.varsStatus.flagLockNow) {
				eleTitle.addClassName('codeLibBaseFontCcc');
				eleTitle.addClassName('codeLibBaseCursorDefault');
			} else {
				eleTitle.addClassName('codeLibBaseCursorPointer');
			}

			eleTitle.addClassName('codeLibTreeTitle');


			/*block*/
			for (var j = 0; j < 1; j++) {
				var eleSpan = $(document.createElement('span'));
				eleSpan.unselectable = 'on';
				eleSpan.addClassName('codeLibTreeBlock');
				eleDiv.insert(eleSpan);
			}

			/*img*/
			var eleImg = $(document.createElement('span'));
			eleImg.addClassName('codeLibTreeBlock');

			if (obj.arr[i].strClassLoad) {
				eleImg.addClassName(obj.arr[i].strClassLoad);
			} else {
				eleImg.addClassName(obj.arr[i].strClass);
			}

			eleImg.unselectable = 'on';
			eleImg.addClassName('codeLibTreeImg');
			eleImg.addClassName('unselect');

			/*order*/
			eleDiv.insert(eleImg);

			/*remove*/
			if (obj.arr[i].flagRemoveUse
				&& this.vars.varsStatus.flagRemoveUse
				&& this.vars.varsStatus.flagRemoveNow
			) {
				var eleRemove = $(document.createElement('span'));
				eleRemove.addClassName('codeLibTreeRemove');
				if (this.vars.varsStatus.flagLockUse && this.vars.varsStatus.flagLockNow) {
					eleRemove.addClassName('codeLibBaseCursorDefault');
				} else {
					eleRemove.addClassName('codeLibBaseCursorPointer');
				}
				eleRemove.unselectable = 'on';
				eleRemove.addClassName('unselect');
				if (!(this.vars.varsStatus.flagLockUse && this.vars.varsStatus.flagLockNow)) {
					this.insListener.set({
						bindAsEvent : 1, insCurrent : this, event : 'mousedown',
						strFunc : '_mousedownRemove', ele : eleRemove, vars : { vars : obj.arr[i] }
					});
				}
				eleDiv.insert(eleRemove);
			}

			/*edit*/
			if (obj.arr[i].flagEditUse
				&& this.vars.varsStatus.flagEditUse
				&& this.vars.varsStatus.flagEditNow
			) {
				var eleEdit = $(document.createElement('span'));
				eleEdit.addClassName('codeLibTreeEdit');
				if (this.vars.varsStatus.flagLockUse && this.vars.varsStatus.flagLockNow) {
					eleEdit.addClassName('codeLibBaseCursorDefault');
				} else {
					eleEdit.addClassName('codeLibBaseCursorPointer');
				}
				eleEdit.unselectable = 'on';
				eleEdit.addClassName('unselect');
				if (!(this.vars.varsStatus.flagLockUse && this.vars.varsStatus.flagLockNow)) {
					this.insListener.set({
						bindAsEvent : 1, insCurrent : this, event : 'mousedown',
						strFunc : '_mousedownEdit', ele : eleEdit,
						vars : { vars : obj.arr[i] }
					});
				}
				eleDiv.insert(eleEdit);
			}

			if (obj.arr[i].strTitle.length > this._staticWhole.numLength) {
				var strTitle = obj.arr[i].strTitle.slice(0, this._staticWhole.numLength);
				eleTitle.insert(strTitle);
			}
			else eleTitle.insert(obj.arr[i].strTitle);

			var eleForm = $(document.createElement('form'));
			eleForm.addClassName('codeLibTreeForm');
			if (obj.arr[i].flagEditUse
				&& this.vars.varsStatus.flagEditUse
				&& this.vars.varsStatus.flagEditNow
			) {
				var eleTag = $(document.createElement('input'));
				eleTag.addClassName('codeLibBaseMarginLeftFive');
				eleTag.addClassName('codeLibTreeFormTag');
				eleTag.value = obj.arr[i].strTitle;
				eleTag.type = 'text';
				eleForm.insert(eleTag);
				eleForm.hide();
			}
			eleDiv.insert(eleTitle);
			eleDiv.insert(eleForm);
			this.eleTree.insert(eleDiv);
			this.eleTree.insert(eleSort);
			if (this.vars.varsStatus.flagBtnUse && obj.arr[i].flagBtnUse) {
				if (!(this.vars.varsStatus.flagLockUse && this.vars.varsStatus.flagLockNow)) {
					this.insListener.set({
						bindAsEvent : 1, insCurrent : this, event : 'dblclick',
						strFunc : '_dblclickBtn', ele : eleDiv, vars : { vars : obj.arr[i] }
					});
					this.insListener.set({
						bindAsEvent : 1, insCurrent : this, event : 'mousedown',
						strFunc : '_mousedownBtn', ele : eleDiv, vars : { vars : obj.arr[i] }
					});
					this.insListener.set({bindAsEvent : 0, insCurrent : this, event : 'mouseover',
						strFunc : '_mouseoverBtn', ele : eleDiv, vars : { vars : obj.arr[i] }
					});
					this.insListener.set({bindAsEvent : 0, insCurrent : this, event : 'mouseout',
						strFunc : '_mouseoutBtn', ele : eleDiv, vars : { vars : obj.arr[i] }
					});
					eleDiv.observe('contextmenu', Event.stop);
					this.insListener.set({bindAsEvent : 0, insCurrent : this, event : 'mouseover',
						strFunc : '_mouseoverTitle', ele : eleTitle, vars : { vars : obj.arr[i] }
					});
					this.insListener.set({bindAsEvent : 0, insCurrent : this, event : 'mouseout',
						strFunc : '_mouseoutTitle', ele : eleTitle, vars : { vars : obj.arr[i] }
					});
				}
			}
			if (obj.arr[i].child.length) {
				this._setFindTemplate({
					arr      : obj.arr[i].child,
					idParent : obj.arr[i].id
				});
			}
		}
	},

	/**
	 *
	*/
	_iniLine : function()
	{
		this.eleTree = $(document.createElement('div'));
		this.eleTree.addClassName('codeLibBaseMarginLeftFive');
		$(this.insRoot.vars.varsSystem.id.root).insert(this.eleTree);
		this.setLineId({
			idParent : this.idSelf ,
			arr      : this.vars.varsDetail
		});
		this._iniCake();
		this._setLine({
			idParent : this.idSelf ,
			arr      : this.vars.varsDetail
		});
		this._setWidth();
		this.insFormat.eleTemplate.body.insert(this.eleTree);
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
	_setLine : function(obj)
	{
		var eleTree;
		if (!obj.parent) {
			eleTree = $(document.createElement('ul'));
			eleTree.unselectable = 'on';
			eleTree.addClassName('codeLibBaseCursorDefault');
			this.eleTree.insert(eleTree);
			obj.parent = eleTree;
		}
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].flagUse) continue;
			obj.arr[i].id = obj.idParent + '-' + i;
			var array = (obj.arr[i].id).split(this.idSelf);
			var level = array[1];
			var arrayLevel = level.split('-');
			var num = (arrayLevel.length) - 2;

			/*sortLine*/
			var eleSort = $(document.createElement('div'));
			eleSort.addClassName('codeLibTreeSort');
			eleSort.unselectable = 'on';

			/*btnLine*/
			var eleDiv = $(document.createElement('div'));
			eleDiv.unselectable = 'on';
			eleDiv.addClassName('codeLibTreeBtn');
			eleDiv.addClassName('unselect');
			eleDiv.id = obj.arr[i].id;

			var arrChild = obj.arr[i].child;
			var numChild = 0;
			for (var j = 0; j < arrChild.length; j++) {
				if (arrChild[j].flagUse) {
					numChild++;
				}
			}
			var children = ' (' + numChild + ')';
			var strInside = '';
			if (obj.arr[i].varsInside) strInside = ' [' + obj.arr[i].varsInside.length + ']';

			/*title*/
			var eleTitle = $(document.createElement('span'));
			eleTitle.unselectable = 'on';
			if (this.vars.varsStatus.flagLockUse && this.vars.varsStatus.flagLockNow) {
				eleTitle.addClassName('codeLibBaseFontCcc');
				eleTitle.addClassName('codeLibBaseCursorDefault');
			}

			if (obj.arr[i].flagChildrenUse && obj.arr[i].flagInsideUse) eleTitle.title = obj.arr[i].strTitle + children + strInside;
			else if (obj.arr[i].flagInsideUse) eleTitle.title = obj.arr[i].strTitle + strInside;
			else if (obj.arr[i].flagChildrenUse) eleTitle.title = obj.arr[i].strTitle + children;
			else eleTitle.title = obj.arr[i].strTitle;


			if (this.vars.varsStatus.flagBtnUse && obj.arr[i].flagBtnUse) {
				if (this.vars.varsStatus.flagLockUse && this.vars.varsStatus.flagLockNow) {
					eleTitle.addClassName('codeLibBaseCursorDefault');

				} else {
					eleTitle.addClassName('codeLibBaseCursorPointer');

				}
			}
			eleTitle.addClassName('codeLibTreeTitle');
			if (!obj.arr[i].flagCheckUse || !this.vars.varsStatus.flagCheckUse) {
				eleTitle.addClassName('codeLibBaseMarginLeftFive');
			}

			/*block*/
			var eleUl = $(document.createElement('ul'));
			for (var j = 0; j < num; j++) {
				var eleSpan = $(document.createElement('span'));
				eleSpan.unselectable = 'on';
				eleSpan.addClassName('codeLibTreeBlock');
				eleDiv.insert(eleSpan);
			}

			/*fold*/
			var eleFold;
			if (obj.arr[i].flagFoldUse && this.vars.varsStatus.flagFoldUse && obj.arr[i].child.length) {
				if (obj.arr[i].flagFoldNow) {
					eleFold = $(document.createElement('span'));
					eleFold.unselectable = 'on';
					if (this.vars.varsStatus.flagLockUse && this.vars.varsStatus.flagLockNow) {
						eleFold.addClassName('codeLibBaseCursorDefault');

					} else {
						eleFold.addClassName('codeLibBaseCursorPointer');

					}

					eleFold.addClassName('codeLibTreeFold');
					eleFold.addClassName('codeLibTreeFoldOpen');

				} else {
					eleFold = $(document.createElement('span'));
					eleFold.unselectable = 'on';
					if (this.vars.varsStatus.flagLockUse && this.vars.varsStatus.flagLockNow) {
						eleFold.addClassName('codeLibBaseCursorDefault');

					} else {
						eleFold.addClassName('codeLibBaseCursorPointer');

					}
					eleUl.style.display = 'none';
					eleFold.addClassName('codeLibTreeFold');
					eleFold.addClassName('codeLibTreeFoldClose');
				}
				eleDiv.insert(eleFold);
				if (!(this.vars.varsStatus.flagLockUse && this.vars.varsStatus.flagLockNow)) {
					this.insListener.set({
						bindAsEvent : 1, insCurrent : this, event : 'mousedown',
						strFunc : '_mousedownFold', ele : eleFold, vars : { vars : obj.arr[i]}
					});
				}

			} else {
				if (this.vars.varsStatus.flagFoldUse) {
					var eleSpan = $(document.createElement('span'));
					eleSpan.unselectable = 'on';
					eleSpan.addClassName('codeLibTreeBlock');
					eleDiv.insert(eleSpan);
				}
			}

			/*img*/
			var eleImg = $(document.createElement('span'));
			eleImg.addClassName('codeLibTreeBlock');
			if (obj.arr[i].strClassLoad) {
				eleImg.addClassName(obj.arr[i].strClassLoad);
			} else {
				eleImg.addClassName(obj.arr[i].strClass);
			}
			eleImg.unselectable = 'on';
			eleImg.addClassName('codeLibTreeImg');
			eleDiv.insert(eleImg);

			if (obj.arr[i].flagMoveUse && this.vars.varsStatus.flagMoveUse) {
				if (!(this.vars.varsStatus.flagLockUse && this.vars.varsStatus.flagLockNow)) {
					this.insListener.set({
						bindAsEvent : 1, insCurrent : this, event : 'mousedown',
						strFunc : '_mousedownMove', ele : eleImg, vars : { vars : obj.arr[i] }
					});

					this.insListener.set({
						bindAsEvent : 0, insCurrent : this, event : 'mouseover',
						strFunc : '_mouseoverSort', ele : eleSort, vars : { vars : obj.arr[i] }
					});
					this.insListener.set({bindAsEvent : 0, insCurrent : this, event : 'mouseout',
						strFunc : '_mouseoutSort', ele : eleSort, vars : { vars : obj.arr[i]}
					});
					this.insListener.set({bindAsEvent : 0, insCurrent : this, event : 'mouseup',
						strFunc : '_mouseupSort', ele : eleSort, vars : { vars : obj.arr[i]}
					});

					this.insListener.set({
						bindAsEvent : 0, insCurrent : this, event : 'mouseover',
						strFunc : '_mouseoverSort', ele : eleTitle, vars : { vars : obj.arr[i] }
					});
					this.insListener.set({bindAsEvent : 0, insCurrent : this, event : 'mouseout',
						strFunc : '_mouseoutSort', ele : eleTitle, vars : { vars : obj.arr[i]}
					});
					this.insListener.set({bindAsEvent : 0, insCurrent : this, event : 'mouseup',
						strFunc : '_mouseupSort', ele : eleTitle, vars : { vars : obj.arr[i]}
					});

					this.insListener.set({bindAsEvent : 0, insCurrent : this, event : 'mouseover',
						strFunc : '_mouseoverImg', ele : eleImg, vars : { vars : obj.arr[i] }
					});
					this.insListener.set({bindAsEvent : 0, insCurrent : this, event : 'mouseout',
						strFunc : '_mouseoutImg', ele : eleImg, vars : { vars : obj.arr[i] }
					});
					this.insListener.set({bindAsEvent : 0, insCurrent : this, event : 'mouseup',
						strFunc : '_mouseupImg', ele : eleImg, vars : { vars : obj.arr[i]}
					});
				}

				if (this.vars.varsStatus.flagLockUse && this.vars.varsStatus.flagLockNow) {
					eleImg.addClassName('codeLibBaseCursorDefault');

				} else {
					eleImg.addClassName('codeLibBaseCursorMove');

				}
			}

			/*check*/
			var eleCheck = $(document.createElement('span'));
			eleCheck.addClassName('codeLibTreeCheck');
			if (obj.arr[i].flagCheckUse && this.vars.varsStatus.flagCheckUse) {
				eleCheck.addClassName('codeLibTreeBlock');
				eleCheck.addClassName('codeLibBaseMarginLeftFive');
				eleCheck.unselectable = 'on';
				eleCheck.addClassName('unselect');
				if (Prototype.Browser.IE) eleCheck.addClassName('ie');
				else if (Prototype.Browser.Gecko) eleCheck.addClassName('firefox');
				else if (navigator.userAgent.match("Chrome")) eleCheck.addClassName('chrome');
				var eleForm = $(document.createElement('form'));
				var eleTag = $(document.createElement('input'));
				eleTag.addClassName('codeLibTreeCheckTag');
				eleTag.value = obj.arr[i].flagCheckNow;
				eleTag.type = 'checkbox';
				if (obj.arr[i].flagCheckNow) eleTag.checked = true;
				eleForm.insert(eleTag);
				eleCheck.insert(eleForm);
				if (!(this.vars.varsStatus.flagLockUse && this.vars.varsStatus.flagLockNow)) {
					this.insListener.set({
						bindAsEvent : 1, insCurrent : this, event : 'mousedown',
						strFunc : '_mousedownCheck', ele : eleCheck, vars : { vars : obj.arr[i] }
					});
				}

			}
			eleDiv.insert(eleCheck);

			/*remove*/
			if (obj.arr[i].flagRemoveUse
				&& this.vars.varsStatus.flagRemoveUse
				&& this.vars.varsStatus.flagRemoveNow
			) {
				var eleRemove = $(document.createElement('span'));
				eleRemove.addClassName('codeLibTreeRemove');
				if (this.vars.varsStatus.flagLockUse && this.vars.varsStatus.flagLockNow) {
					eleRemove.addClassName('codeLibBaseCursorDefault');
				} else {
					eleRemove.addClassName('codeLibBaseCursorPointer');
				}
				eleRemove.unselectable = 'on';
				eleRemove.addClassName('unselect');
				if (!(this.vars.varsStatus.flagLockUse && this.vars.varsStatus.flagLockNow)) {
					this.insListener.set({
						bindAsEvent : 1, insCurrent : this, event : 'mousedown',
						strFunc : '_mousedownRemove', ele : eleRemove, vars : { vars : obj.arr[i] }
					});
				}

				eleDiv.insert(eleRemove);
			}

			/*edit*/
			if (obj.arr[i].flagEditUse
				&& this.vars.varsStatus.flagEditUse
				&& this.vars.varsStatus.flagEditNow
			) {
				var eleEdit = $(document.createElement('span'));
				eleEdit.addClassName('codeLibTreeEdit');
				if (this.vars.varsStatus.flagLockUse && this.vars.varsStatus.flagLockNow) {
					eleEdit.addClassName('codeLibBaseCursorDefault');
				} else {
					eleEdit.addClassName('codeLibBaseCursorPointer');
				}
				eleEdit.unselectable = 'on';
				eleEdit.addClassName('unselect');
				if (!(this.vars.varsStatus.flagLockUse && this.vars.varsStatus.flagLockNow)) {
					this.insListener.set({
						bindAsEvent : 1, insCurrent : this, event : 'mousedown',
						strFunc : '_mousedownEdit', ele : eleEdit, vars : { vars : obj.arr[i] }
					});
				}

				eleDiv.insert(eleEdit);
			}
			if (obj.arr[i].strTitle.length > this._staticWhole.numLength) {
				var strTitle = obj.arr[i].strTitle.slice(0, this._staticWhole.numLength);

				if (obj.arr[i].flagChildrenUse && obj.arr[i].flagInsideUse) eleTitle.insert(obj.arr[i].strTitle + children + strInside);
				else if (obj.arr[i].flagInsideUse) eleTitle.insert(obj.arr[i].strTitle + strInside);
				else if (obj.arr[i].flagChildrenUse) eleTitle.insert(obj.arr[i].strTitle + children);
				else eleTitle.insert(strTitle);

			} else {
				if (obj.arr[i].flagChildrenUse && obj.arr[i].flagInsideUse) eleTitle.insert(obj.arr[i].strTitle + children + strInside);
				else if (obj.arr[i].flagInsideUse) eleTitle.insert(obj.arr[i].strTitle + strInside);
				else if (obj.arr[i].flagChildrenUse) eleTitle.insert(obj.arr[i].strTitle + children);
				else eleTitle.insert(obj.arr[i].strTitle);
			}

			if (this.vars.varsStatus.flagFontUse) {
				eleTitle.addClassName(obj.arr[i].strClassFont);
			}
			if (this.vars.varsStatus.flagBoldUse && obj.arr[i].flagBoldNow) {
				eleTitle.addClassName('codeLibBaseFontBold');
			}

			var eleForm = $(document.createElement('form'));
			eleForm.addClassName('codeLibTreeForm');
			if (obj.arr[i].flagEditUse
				&& this.vars.varsStatus.flagEditUse
				&& this.vars.varsStatus.flagEditNow
			) {
				var eleTag = $(document.createElement('input'));
				eleTag.addClassName('codeLibBaseMarginLeftFive');
				eleTag.addClassName('codeLibTreeFormTag');
				eleTag.value = obj.arr[i].strTitle;
				eleTag.type = 'text';
				eleForm.insert(eleTag);
				eleForm.hide();
			}

			eleDiv.insert(eleTitle);
			eleDiv.insert(eleForm);
			obj.parent.insert(eleDiv);
			obj.parent.insert(eleSort);
			obj.parent.insert(eleUl);

			if (this.vars.varsStatus.flagBtnUse && obj.arr[i].flagBtnUse) {
				if (!(this.vars.varsStatus.flagLockUse && this.vars.varsStatus.flagLockNow)) {
					this.insListener.set({
						bindAsEvent : 1, insCurrent : this, event : 'dblclick',
						strFunc : '_dblclickBtn', ele : eleDiv, vars : { vars : obj.arr[i] }
					});
					this.insListener.set({
						bindAsEvent : 1, insCurrent : this, event : 'mousedown',
						strFunc : '_mousedownBtn', ele : eleDiv, vars : { vars : obj.arr[i] }
					});
					this.insListener.set({
						bindAsEvent : 0, insCurrent : this, event : 'mouseover',
						strFunc : '_mouseoverBtn', ele : eleDiv, vars : { vars : obj.arr[i] }
					});
					this.insListener.set({bindAsEvent : 0, insCurrent : this, event : 'mouseout',
						strFunc : '_mouseoutBtn', ele : eleDiv, vars : { vars : obj.arr[i] }
					});
					eleDiv.observe('contextmenu', Event.stop);
					this.insListener.set({
						bindAsEvent : 0, insCurrent : this, event : 'mouseover',
						strFunc : '_mouseoverTitle', ele : eleTitle, vars : { vars : obj.arr[i] }
					});
					this.insListener.set({
						bindAsEvent : 0, insCurrent : this, event : 'mouseout',
						strFunc : '_mouseoutTitle', ele : eleTitle, vars : { vars : obj.arr[i] }
					});
				}

			}
			if (obj.arr[i].child.length) {
				this._setLine({
					arr      : obj.arr[i].child,
					idParent : obj.arr[i].id,
					parent   : eleUl
				});
			}
		}
	},

	/**
	 *
	*/
	_varsBtn : null,
	_iniBtn : function()
	{
		this._setBtnListener();
		if (!this._varsBtn) return;
		this._removeBtnSelect({arr : this.vars.varsDetail});
		this._setBtnSelect({
			id  : this._varsBtn.id,
			arr : this.vars.varsDetail
		});
	},

	/**
	 *
	*/
	_setBtnListener : function()
	{
		if (this.vars.varsStatus.flagLockUse && this.vars.varsStatus.flagLockNow) return;
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
		if (!this._varsBtn) return;
		this._varsBtn = null;
		this._removeBtnSelect({arr : this.vars.varsDetail});
	},

	/**
	 *
	*/
	_dblclickBtn : function(evt, obj)
	{
		evt.stop();
		if (this._varsEdit.flag) return;
		this.allot({
			insCurrent : this.insCurrent,
			from       : '_dblclickBtn',
			evt        : evt,
			vars       : obj.vars
		});
	},

	/**
	 *
	*/
	setLock : function()
	{
		if (!this.vars.varsStatus.flagLockUse) return;
		this.vars.varsStatus.flagLockNow = 1;
		this.iniReload();
	},


	/**
	 *
	*/
	cancelLock : function()
	{
		if (!this.vars.varsStatus.flagLockUse) return;
		this.vars.varsStatus.flagLockNow = 0;
		this.iniReload();
	},


	/**
	 *
	*/
	getBtnSelect : function()
	{
		return this._varsBtn;
	},

	/**
	 *
	*/
	setBtnSelect : function(obj)
	{
		this._varsBtn = obj.vars;
		this._setBtnSelect({
			id  : obj.vars.id,
			arr : this.vars.varsDetail
		});
	},

	/**
	 *
	*/
	_mousedownBtn : function(evt, obj)
	{
		evt.stop();
		if (this._varsEdit.flag) return;
		this._varsBtn = obj.vars;
		this._removeBtnSelect({arr : this.vars.varsDetail});
		this._setBtnSelect({
			id  : obj.vars.id,
			arr : this.vars.varsDetail
		});
		this.setLock();
		var flag = this.allot({
			insCurrent : this.insCurrent,
			from       : '_mousedownBtn',
			evt        : evt,
			vars       : obj.vars
		});
		if(flag) this.cancelLock();
	},

	/**
	 *
	*/
	_mouseoverBtn : function(obj)
	{
		if (this._varsMove.flag) return;
		$(obj.vars.id).addClassName('codeLibTreeBtnOver');
	},

	/**
	 *
	*/
	_mouseoutBtn : function(obj)
	{
		if (this._varsMove.flag) return;
		$(obj.vars.id).removeClassName('codeLibTreeBtnOver');
	},

	/**
	 *
	*/
	_setBtnSelect : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].flagUse) continue;
			if (obj.arr[i].child.length) this._setBtnSelect({arr:obj.arr[i].child,id : obj.id});
			if (!$(obj.arr[i].id) || (this.vars.varsStatus.flagBtnUse && !obj.arr[i].flagBtnUse)) continue;
			if (obj.arr[i].id == obj.id) {
				this._removeBtnSelectLoad({
					vars : obj.arr[i]
				});
				$(obj.arr[i].id).addClassName('codeLibTreeBtnSelect');
			}
		}
	},

	/**
	 *
	*/
	removeBtnSelect : function()
	{
		this._varsBtn = null;
		this._removeBtnSelect({arr : this.vars.varsDetail});
	},

	/**
	 *
	*/
	_removeBtnSelectLoad : function(obj)
	{
		if (!obj.vars.strClassLoad) return;
		$(obj.vars.id).down('.codeLibTreeImg', 0).removeClassName(obj.vars.strClassLoad);
		obj.vars.strClassLoad = '';

		$(obj.vars.id).down('.codeLibTreeImg', 0).addClassName(obj.vars.strClass);
	},

	/**
	 *
	*/
	_removeBtnSelect : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].flagUse) continue;
			if (obj.arr[i].child.length) this._removeBtnSelect({arr:obj.arr[i].child});
			if (!$(obj.arr[i].id) || (this.vars.varsStatus.flagBtnUse && !obj.arr[i].flagBtnUse)) continue;
			$(obj.arr[i].id).removeClassName('codeLibTreeBtnSelect');
		}
	},

	/**
	 *
	*/
	_setFoldUpdate : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].flagUse) continue;
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
			this._setWidth();
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
		this.setCake({arr : this.vars.varsDetail});
	},

	/**
	 *
	*/
	_varsFold : {now : 0, num : 0},
	_checkFoldNow : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].flagUse) continue;
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
			if (!obj.arr[i].flagUse) continue;
			if (obj.arr[i].id == obj.vars.id) {
				var zIndex = this.insRoot.setZIndex();
				var idInsert = this.insRoot.vars.varsSystem.id.root;
				var insTree = this.insSelf;
				if (obj.flagEffect) {
					$(obj.arr[i].id).down('.codeLibTreeFold', 0).removeClassName('codeLibTreeFoldOpen');
					$(obj.arr[i].id).down('.codeLibTreeFold', 0).removeClassName('codeLibTreeFoldClose');
					if (obj.arr[i].flagFoldNow) {
						$(obj.arr[i].id).down('.codeLibTreeFold', 0).addClassName('codeLibTreeFoldOpen');
						new Effect.BlindDown($(obj.arr[i].id).next('ul', 0),{
							duration : 0.5
						});
					} else {
						$(obj.arr[i].id).down('.codeLibTreeFold', 0).addClassName('codeLibTreeFoldClose');
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
	_setFoldUpdateStyleWrap : function()
	{
		$(this.insRoot.vars.varsSystem.id.root).insert(this.eleTree);
		this._setWidth();
		this.insFormat.eleTemplate.body.insert(this.eleTree);
	},

	/**
	 *
	*/
	_setFoldRestoreStyle : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].flagUse) continue;
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
	_updateFoldVarAll : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].flagUse) continue;
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
	_setFoldUpdateStyleAll : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].flagUse) continue;
			if (obj.arr[i].flagFoldUse) {
				if (obj.flagEffect && $(obj.arr[i].id).down('.codeLibTreeFold', 0)) {
					$(obj.arr[i].id).down('.codeLibTreeFold', 0).removeClassName('codeLibTreeFoldOpen');
					$(obj.arr[i].id).down('.codeLibTreeFold', 0).removeClassName('codeLibTreeFoldClose');
					if (obj.arr[i].flagFoldNow) {
						$(obj.arr[i].id).down('.codeLibTreeFold', 0).addClassName('codeLibTreeFoldOpen');
						$(obj.arr[i].id).next('ul', 0).show();
					} else {
						$(obj.arr[i].id).down('.codeLibTreeFold', 0).addClassName('codeLibTreeFoldClose');
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
			if (!obj.arr[i].flagUse) continue;
			if (!obj.arr[i].flagFoldNow) $(obj.arr[i].id).next('ul', 0).show();
			else $(obj.arr[i].id).next('ul', 0).hide();
			if (obj.arr[i].child.length) this._setFoldRestoreStyleAll({arr:obj.arr[i].child});
		}

		return obj.arr;
	},

	/**
	 *
	*/
	_setImgStyleCursor : function(obj)
	{
		if (this.vars.varsStatus.flagLockUse && this.vars.varsStatus.flagLockNow) return;
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].flagUse) continue;
			if (!obj.arr[i].flagMoveUse || !$(obj.arr[i].id)) continue;
			$(obj.arr[i].id).down('.codeLibTreeImg', 0).addClassName('codeLibBaseCursorMove');
			if (obj.arr[i].child.length) this._setImgStyleCursor({arr:obj.arr[i].child});
		}
	},

	/**
	 *
	*/
	_removeImgStyleCursor : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].flagUse) continue;
			if (!obj.arr[i].flagMoveUse || !$(obj.arr[i].id)) continue;
			$(obj.arr[i].id).down('.codeLibTreeImg', 0).removeClassName('codeLibBaseCursorMove');
			if (obj.arr[i].child.length) this._removeImgStyleCursor({arr : obj.arr[i].child});
		}
	},

	/**
	 *
	*/
	_mouseoverImg : function(obj)
	{
		if (!this.vars.varsStatus.flagMoveUse) return;
		if (this._varsMove.flag) {
			$(obj.vars.id).down('.codeLibTreeImg', 0).removeClassName('codeLibBaseCursorMove');
			var flag = this._checkChild({vars:obj.vars});
			if (this._varsMove.mousedownId == obj.vars.id || flag || !obj.vars.flagInsertUse) {
				this._varsMove.mouseupId = '';
				this._varsMove.mouseupAction = '';
			} else {
				$(obj.vars.id).down('.codeLibTreeTitle', 0).addClassName('codeLibTreeTitleInsert');
				this._varsMove.mouseupId = obj.vars.id;
				this._varsMove.mouseupAction = 'insert';
				this._setImgTime();
			}
		}
	},

	/**
	 *
	*/
	_mouseoutImg : function(obj)
	{
		if (!this.vars.varsStatus.flagMoveUse) return;
		if (this._varsMove.flag) {
			this._removeImgTimer();
			this._varsMove.mouseupId = '';
			this._varsMove.mouseupAction = '';
			$(obj.vars.id).down('.codeLibTreeTitle', 0).removeClassName('codeLibTreeTitleInsert');
		}
	},

	/**
	 *
	*/
	_mouseupImg : function(obj)
	{
		if (!this.vars.varsStatus.flagMoveUse) return;
		if (this._varsMove.flag) {
			this._removeImgTimer();
			$(obj.vars.id).down('.codeLibTreeTitle', 0).removeClassName('codeLibTreeTitleInsert');
		}
	},

	/**
	 *
	*/
	_getImgTimerVars : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].flagUse) continue;
			if (!obj.arr[i].flagMoveUse || !$(obj.arr[i].id)) continue;
			if (obj.arr[i].id == this._varsMove.mouseupId) {
				this._varsMove.timerVars = obj.arr[i];
				return;
			}
			if (obj.arr[i].child.length) this._getImgTimerVars({arr:obj.arr[i].child});
		}
	},

	/**
	 *
	*/
	_setImgTime : function()
	{
		this._getImgTimerVars({arr:this.vars.varsDetail});
		this._varsMove.timerFlag = 1;
		this._varsMove.timer = setInterval(this._setImgTimer.bind(this), this._staticMove.numTimer);
	},

	/**
	 *
	*/
	_setImgTimer : function()
	{
		if (!this._varsMove.timerFlag) return;
		if ($(this._varsMove.mouseupId).next('ul', 0).style.display != 'none') return;
		this._removeImgTimer();
		this._setFold({vars:this._varsMove.timerVars});
	},

	/**
	 *
	*/
	_removeImgTimer : function()
	{
		this._varsMove.timerFlag = 0;
		clearInterval(this._varsMove.timer);
		this._varsMove.timer = null;
	},

	/**
	 *
	*/
	_mouseoverSort : function(obj)
	{
		if (!this.vars.varsStatus.flagMoveUse) return;
		if (!this.vars.varsStatus.flagSortUse) return;
		var cut = this._varsMove;
		if (cut.flag) {
			var flag = this._checkChild({vars : obj.vars});
			if (cut.vars.id == obj.vars.id || flag) {
				cut.mouseupId = '';
				cut.mouseupAction = '';
				$(obj.vars.id).next('.codeLibTreeSort', 0).removeClassName('codeLibTreeSortOver');
			} else {
				cut.mouseupId = obj.vars.id;
				cut.mouseupAction = 'after';
				$(obj.vars.id).next('.codeLibTreeSort', 0).addClassName('codeLibTreeSortOver');
			}
		}
	},

	/**
	 *
	*/
	_checkChild : function(obj)
	{
		var array = (obj.vars.id).split(this.idSelf);
		var level = array[1];
		arrayLevel = level.split('-');
		var num = this._varsMove.arrayLevel.length;
		var flag = 0;
		for (var i = 0; i < this._varsMove.arrayLevel.length; i++) {
			if (arrayLevel[i] == this._varsMove.arrayLevel[i]) num--;
		}
		if (num==0) return 1;
		else return 0;
	},

	/**
	 *
	*/
	_mouseoutSort : function(obj)
	{
		if (!this.vars.varsStatus.flagMoveUse) return;
		if (!this.vars.varsStatus.flagSortUse) return;
		var cut = this._varsMove;
		if (cut.flag) {
			cut.mouseupId = '';
			cut.mouseupAction = '';
			$(obj.vars.id).next('.codeLibTreeSort', 0).removeClassName('codeLibTreeSortOver');
		}
	},

	/**
	 *
	*/
	_mouseupSort : function(obj)
	{
		if (!this.vars.varsStatus.flagMoveUse) return;
		if (!this.vars.varsStatus.flagSortUse) return;
		var cut = this._varsMove;
		if (cut.flag) {
			$(obj.vars.id).next('.codeLibTreeSort', 0).removeClassName('codeLibTreeSortOver');
		}
	},


	/**
	 *
	*/
	_mousedownBarEdit : function(evt, obj)
	{
		if (obj) evt.stop();
		else obj = evt;
		this._updateBarEditVar();
		this._updateBarEditStyle();
		this.setCakeBar();
		this.iniReload();
	},

	/**
	 *
	*/
	_updateBarEditVar : function()
	{
		if (this.vars.varsStatus.flagEditNow) {
			this.vars.varsStatus.flagEditNow = 0;
		} else {
			this.vars.varsStatus.flagEditNow = 1;
		}
	},

	/**
	 *
	*/
	_updateBarEditStyle : function()
	{
		var ele = this.eleWrapBar.down('.codeLibTreeBarEdit', 0);
		ele.removeClassName('codeLibTreeBarEditOn');
		ele.removeClassName('codeLibTreeBarEditOff');
		if (!this.vars.varsStatus.flagEditNow) {
			ele.addClassName('codeLibTreeBarEditOff');
		} else {
			ele.addClassName('codeLibTreeBarEditOn');
		}
	},

	/**
	 *
	*/
	_mousedownBarRemove : function(evt, obj)
	{
		if (obj) evt.stop();
		else obj = evt;
		/*
		this._updateBarRemoveVar();
		this._updateBarRemoveStyle();
		this.setCakeBar();
		*/
		this.vars.varsDetail = [];
		var flag = this.allot({
			insCurrent : this.insCurrent,
			from       : '_mousedownBarRemove'
		});
		this.iniReload();
	},

	/**
	 *
	*/
	_updateBarRemoveVar : function()
	{
		if (this.vars.varsStatus.flagRemoveNow) {
			this.vars.varsStatus.flagRemoveNow = 0;
		} else {
			this.vars.varsStatus.flagRemoveNow = 1;
		}
	},

	/**
	 *
	*/
	_updateBarRemoveStyle : function()
	{
		var ele = this.eleWrapBar.down('.codeLibTreeBarRemove', 0);
		ele.removeClassName('codeLibTreeBarRemoveOn');
		ele.removeClassName('codeLibTreeBarRemoveOff');
		if (!this.vars.varsStatus.flagRemoveNow) {
			ele.addClassName('codeLibTreeBarRemoveOff');
		} else {
			ele.addClassName('codeLibTreeBarRemoveOn');
		}
	},

	/**
	 *
	*/
	_mousedownBarCheck : function(evt, obj)
	{
		if (obj) evt.stop();
		else obj = evt;
		this._updateBarCheckVar();
		this._updateBarCheckStyle();
		this.setCakeBar();
	},

	/**
	 *
	*/
	_updateBarCheckVar : function()
	{
		if (this.vars.varsStatus.flagCheckNow) {
			this.vars.varsStatus.flagCheckNow = 0;
			this._updateCheckVarAll({
				arr          : this.vars.varsDetail,
				flagCheckNow : 0
			});
		} else {
			this.vars.varsStatus.flagCheckNow = 1;
			this._updateCheckVarAll({
				arr          : this.vars.varsDetail,
				flagCheckNow : 1
			});
		}
	},

	/**
	 *
	*/
	_updateBarCheckStyle : function()
	{
		var ele = this.eleWrapBar.down('.codeLibTreeBarCheck', 0);
		ele.removeClassName('codeLibTreeBarCheckUnchecked');
		ele.removeClassName('codeLibTreeBarCheckChecked');
		if (!this.vars.varsStatus.flagCheckNow) {
			ele.addClassName('codeLibTreeBarCheckChecked');
			this._updateCheckStyleAll({
				arr          : this.vars.varsDetail,
				flagCheckNow : 0
			});
		} else {
			ele.addClassName('codeLibTreeBarCheckUnchecked');
			this._updateCheckStyleAll({
				arr          : this.vars.varsDetail,
				flagCheckNow : 1
			});
		}
	},

	/**
	 *
	*/
	_mousedownCheck : function(evt, obj)
	{
		if (obj) evt.stop();
		else obj = evt;
		this._updateCheckVar({
			arr  : this.vars.varsDetail,
			vars : obj.vars
		});
		this.allot({
			insCurrent : this.insCurrent,
			from       : '_mousedownCheck',
			vars       : obj.vars
		});
	},

	/**
	 *
	*/
	setCheckAllValue : function(obj)
	{
		this._setCheckAllValueChild({arr : this.vars.varsDetail});
	},


	/**
	 *
	*/
	_setCheckAllValueChild : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].flagUse) continue;
			if (obj.arr[i].flagCheckUse) {
				obj.arr[i].flagCheckNow = ($(obj.arr[i].id).down('.codeLibTreeCheckTag', 0).checked == true)? 1 : 0;
			}
			if (obj.arr[i].child.length) {
				this._setCheckAllValueChild({arr : obj.arr[i].child});
			}
		}

		return obj.arr;
	},

	/**
	 *
	*/
	_updateCheckStyleAll : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].flagUse) continue;
			if (obj.arr[i].flagCheckUse) {
				if (obj.flagCheckNow) {
					$(obj.arr[i].id).down('.codeLibTreeCheckTag', 0).value = 0;
					$(obj.arr[i].id).down('.codeLibTreeCheckTag', 0).checked = false;
				} else {
					$(obj.arr[i].id).down('.codeLibTreeCheckTag', 0).value = 1;
					$(obj.arr[i].id).down('.codeLibTreeCheckTag', 0).checked = true;
				}
			}
			if (obj.arr[i].child.length) {
				this._updateCheckStyleAll({arr : obj.arr[i].child, flagCheckNow : obj.flagCheckNow});
			}
		}
	},

	/**
	 *
	*/
	_updateCheckVarAll : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].flagUse) continue;
			if (obj.arr[i].flagCheckUse) {
				if (obj.flagCheckNow) obj.arr[i].flagCheckNow = 1;
				else obj.arr[i].flagCheckNow = 0;
			}
			if (obj.arr[i].child.length) {
				this._updateCheckVarAll({arr : obj.arr[i].child, flagCheckNow : obj.flagCheckNow});
			}
		}
	},

	/**
	 *
	*/
	_updateCheckVar : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].flagUse) continue;
			if (obj.arr[i].flagCheckUse) {
				if (obj.arr[i].id == obj.vars.id) {
					if (obj.arr[i].flagCheckNow) obj.arr[i].flagCheckNow = 0;
					else obj.arr[i].flagCheckNow = 1;
				}
			}
			if (obj.arr[i].child.length) {
				this._updateCheckVar({arr:obj.arr[i].child, vars : obj.vars});
			}
		}
	},

	/**
	 *
	*/
	_setBarFoldListener : function(obj)
	{
		if (this.vars.varsStatus.flagLockUse && this.vars.varsStatus.flagLockNow) return;
		var ele = obj.ele;
		this.insListener.set({
			bindAsEvent : 1, insCurrent : this, event : 'mousedown',
			strFunc : '_mousedownBarFold', ele : ele, vars : ''
		});
	},

	/**
	 *
	*/
	_mousedownBarFold : function(evt, obj)
	{
		if (obj) evt.stop();
		else obj = evt;

		if (this.vars.varsFind.num) return;
		this._updateBarFoldVar();
		this._updateBarFoldStyle();
		this.setCake({arr : this.vars.varsDetail});
		this.setCakeBar();
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
	_updateBarFoldStyle : function()
	{
		var ele = this.eleWrapBar.down('.codeLibTreeBarFold', 0);
		ele.removeClassName('codeLibTreeFoldOpen');
		ele.removeClassName('codeLibTreeFoldClose');
		if (this.vars.varsStatus.flagFoldNow) ele.addClassName('codeLibTreeFoldOpen');
		else ele.addClassName('codeLibTreeFoldClose');
		$(this.insRoot.vars.varsSystem.id.root).insert(this.eleTree);
		this._setFoldUpdateStyleAll({
			arr        : this.vars.varsDetail,
			flagEffect : 0
		});
		this._setWidth();
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
	insBar : null,
	_iniBar : function()
	{
		if (!this.vars.varsStatus.flagBarUse) return;
		this._wrapBar();
		this._setBar();
	},

	/**
	 *
	*/
	_staticBar : {numHeight : 16, numWidth : 16},
	eleWrapBar : null,
	_wrapBar : function()
	{
		var ele = $(document.createElement('div'));
		ele.addClassName('codeLibTreeBarWrap');
		ele.unselectable = 'on';
		ele.addClassName('unselect');
		this.insFormat.eleTemplate.header.down('.codeLibBaseMarginLeftFive', 0).insert(ele);
		ele.setStyle({
			height : this._staticBar.numHeight + 'px'
		});
		this.eleWrapBar = ele;
	},

	/**
	 *
	*/
	_setBar : function(obj)
	{
		if (this.vars.varsStatus.flagFoldUse) {
			var ele = $(document.createElement('span'));
			ele.addClassName('codeLibTreeBarFold');
			if (this.vars.varsStatus.flagFoldUse) {
				if (this.vars.varsStatus.flagLockUse && this.vars.varsStatus.flagLockNow) {
					ele.addClassName('codeLibBaseCursorDefault');
				} else {
					ele.addClassName('codeLibBaseCursorPointer');
				}
				if (!this.vars.varsStatus.flagFoldNow) ele.addClassName('codeLibTreeFoldClose');
				else  ele.addClassName('codeLibTreeFoldOpen');
				ele.setStyle({
					width  : this._staticBar.numWidth + 'px',
					height : this._staticBar.numHeight + 'px'
				});
			}
			ele.unselectable = 'on';
			ele.addClassName('unselect');
			this.eleWrapBar.insert(ele);
			if (this.vars.varsStatus.strFoldTitle) {
				var eleTitle = $(document.createElement('span'));
				eleTitle.unselectable = 'on';
				eleTitle.addClassName('unselect');
				eleTitle.addClassName('codeLibTreeColumnTitle');
				eleTitle.insert(this.vars.varsStatus.strFoldTitle);
				this.eleWrapBar.insert(eleTitle);
			}
			if (!(this.vars.varsStatus.flagLockUse && this.vars.varsStatus.flagLockNow)) {
				this.insListener.set({
					bindAsEvent : 1, insCurrent : this, event : 'mousedown',
					strFunc : '_mousedownBarFold', ele : ele, vars : ''
				});
			}

		}
		if (this.vars.varsStatus.flagCheckUse) {
			var ele = $(document.createElement('span'));
			ele.addClassName('codeLibTreeBarCheck');
			if (this.vars.varsStatus.flagCheckUse) {
				ele.addClassName('codeLibTreeBarCheckUnchecked');
				if (this.vars.varsStatus.flagLockUse && this.vars.varsStatus.flagLockNow) {
					ele.addClassName('codeLibBaseCursorDefault');
				} else {
					ele.addClassName('codeLibBaseCursorPointer');
				}
				ele.setStyle({
					width  : this._staticBar.numWidth + 'px',
					height : this._staticBar.numHeight + 'px'
				});
			}
			ele.unselectable = 'on';
			ele.addClassName('unselect');
			this.eleWrapBar.insert(ele);
			if (this.vars.varsStatus.strCheckTitle) {
				var eleTitle = $(document.createElement('span'));
				eleTitle.unselectable = 'on';
				eleTitle.addClassName('unselect');
				eleTitle.addClassName('codeLibTreeColumnTitle');
				eleTitle.insert(this.vars.varsStatus.strCheckTitle);
				this.eleWrapBar.insert(eleTitle);
			}
			if (!(this.vars.varsStatus.flagLockUse && this.vars.varsStatus.flagLockNow)) {
				this.insListener.set({
					bindAsEvent : 1, insCurrent : this, event : 'mousedown',
					strFunc : '_mousedownBarCheck', ele : ele, vars : ''
				});
			}
		}
		if (this.vars.varsStatus.flagLinkUse) {
			var ele = $(document.createElement('span'));
			ele.addClassName('codeLibTreeBarLink');
			if (this.vars.varsStatus.flagLinkUse) {
				if (this.vars.varsStatus.flagLockUse && this.vars.varsStatus.flagLockNow) {
					ele.addClassName('codeLibBaseCursorDefault');
				} else {
					ele.addClassName('codeLibBaseCursorPointer');
				}
				ele.setStyle({
					width  : this._staticBar.numWidth + 'px',
					height : this._staticBar.numHeight + 'px'
				});

			}
			ele.unselectable = 'on';
			ele.addClassName('unselect');
			this.eleWrapBar.insert(ele);
			if (this.vars.varsStatus.strLinkTitle) {
				var eleTitle = $(document.createElement('span'));
				eleTitle.unselectable = 'on';
				eleTitle.addClassName('unselect');
				eleTitle.addClassName('codeLibTreeColumnTitle');
				eleTitle.insert(this.vars.varsStatus.strLinkTitle);
				this.eleWrapBar.insert(eleTitle);
			}

			if (!(this.vars.varsStatus.flagLockUse && this.vars.varsStatus.flagLockNow)) {
				this.insListener.set({
					bindAsEvent : 1, insCurrent : this, event : 'mousedown',
					strFunc : '_mousedownBarLink', ele : ele, vars : ''
				});
			}
		}
		if (this.vars.varsStatus.flagAddUse) {
			var ele = $(document.createElement('span'));
			ele.addClassName('codeLibTreeBarAdd');
			if (this.vars.varsStatus.flagAddUse) {
				if (this.vars.varsStatus.flagLockUse && this.vars.varsStatus.flagLockNow) {
					ele.addClassName('codeLibBaseCursorDefault');
				} else {
					ele.addClassName('codeLibBaseCursorPointer');
				}
				ele.setStyle({
					width  : this._staticBar.numWidth + 'px',
					height : this._staticBar.numHeight + 'px'
				});
			}
			ele.unselectable = 'on';
			ele.addClassName('unselect');
			this.eleWrapBar.insert(ele);
			if (this.vars.varsStatus.strAddTitle) {
				var eleTitle = $(document.createElement('span'));
				eleTitle.unselectable = 'on';
				eleTitle.addClassName('unselect');
				eleTitle.addClassName('codeLibTreeColumnTitle');
				eleTitle.insert(this.vars.varsStatus.strAddTitle);
				this.eleWrapBar.insert(eleTitle);
			}

			if (!(this.vars.varsStatus.flagLockUse && this.vars.varsStatus.flagLockNow)) {
				this.insListener.set({
					bindAsEvent : 1, insCurrent : this, event : 'mousedown',
					strFunc : '_mousedownBarAdd', ele : ele, vars : ''
				});
			}
		}

		if (this.vars.varsStatus.flagRemoveUse && !this.vars.varsStatus.flagRemoveMenuNoneUse) {
			var ele = $(document.createElement('span'));
			ele.addClassName('codeLibTreeBarRemove');
			if (this.vars.varsStatus.flagRemoveUse) {
				if (this.vars.varsStatus.flagLockUse && this.vars.varsStatus.flagLockNow) {
					ele.addClassName('codeLibBaseCursorDefault');
				} else {
					ele.addClassName('codeLibBaseCursorPointer');
				}
				ele.setStyle({
					width  : this._staticBar.numWidth + 'px',
					height : this._staticBar.numHeight + 'px'
				});
			}
			if (this.vars.varsStatus.flagRemoveNow) ele.addClassName('codeLibTreeBarRemoveOn');
			else  ele.addClassName('codeLibTreeBarRemoveOff');
			ele.unselectable = 'on';
			ele.addClassName('unselect');
			this.eleWrapBar.insert(ele);
			if (this.vars.varsStatus.strRemoveTitle) {
				var eleTitle = $(document.createElement('span'));
				eleTitle.unselectable = 'on';
				eleTitle.addClassName('unselect');
				eleTitle.addClassName('codeLibTreeColumnTitle');
				eleTitle.insert(this.vars.varsStatus.strRemoveTitle);
				this.eleWrapBar.insert(eleTitle);
			}

			if (!(this.vars.varsStatus.flagLockUse && this.vars.varsStatus.flagLockNow)) {
				this.insListener.set({
					bindAsEvent : 1, insCurrent : this, event : 'mousedown',
					strFunc : '_mousedownBarRemove', ele : ele, vars : ''
				});
			}

		}
		if (this.vars.varsStatus.flagEditUse) {
			/*
			var ele = $(document.createElement('span'));
			ele.addClassName('codeLibTreeBarEdit');
			if (this.vars.varsStatus.flagEditUse) {
				if (this.vars.varsStatus.flagLockUse && this.vars.varsStatus.flagLockNow) {
					ele.addClassName('codeLibBaseCursorDefault');
				} else {
					ele.addClassName('codeLibBaseCursorPointer');
				}
				ele.setStyle({
					width  : this._staticBar.numWidth + 'px',
					height : this._staticBar.numHeight + 'px'
				});
			}
			if (this.vars.varsStatus.flagEditNow) ele.addClassName('codeLibTreeBarEditOn');
			else  ele.addClassName('codeLibTreeBarEditOff');
			ele.unselectable = 'on';
			ele.addClassName('unselect');
			this.eleWrapBar.insert(ele);
			if (!(this.vars.varsStatus.flagLockUse && this.vars.varsStatus.flagLockNow)) {
				this.insListener.set({
					bindAsEvent : 1, insCurrent : this, event : 'mousedown',
					strFunc : '_mousedownBarEdit', ele : ele, vars : ''
				});
			}
			*/
		}
	},

	/**
	 *
	*/
	_iniCakeBar : function()
	{
		if (!this.insRoot.insCake) return;
		this.insRoot.insCake.getStorageCake({
			parentKey  : this.idSelf + 'Bar',
			funcReturn : this._getCakeBarVars,
			insReturn  : this.insSelf
		});
	},

	/**
	 *
	*/
	_getCakeBarVars : function(obj)
	{
		var insCurrent = obj.insReturn;
		if (obj.data) {
			insCurrent._getCakeBarVarsUpdate({data : obj.data});
		}
	},

	/**
	 *
	*/
	_getCakeBarVarsUpdate : function(obj)
	{
		if (!this.vars.varsStatus.flagCakeUse) return;
		var array = this._staticCakeBar;
		for (var i = 0; i < array.length; i++) {
			var str = array[i].capitalize();
			if (this.vars.varsStatus['flag' + str + 'Use']) {
				this.vars.varsStatus['flag' + str + 'Now'] = obj.data['flag' + str + 'Now'];
			}
		}
	},

	/**
	 *
	*/
	_staticCakeBar : ['edit','remove','check','fold'],
	_varsCakeBar : {},
	setCakeBar : function()
	{
		if (!this.insRoot.insCake) return;
		this._varsCakeBar= {};
		this._setCakeBarVars();
		this.insRoot.insCake.setStorageCake({
			parentKey  : this.idSelf + 'Bar',
			value      : this._varsCakeBar,
			numExpires : 0
		});
	},

	/**
	 *
	*/
	_setCakeBarVars : function()
	{
		if (!this.vars.varsStatus.flagCakeUse) return;
		var array = this._staticCakeBar;
		for (var i = 0; i < array.length; i++) {
			var str = array[i].capitalize();
			if (this.vars.varsStatus['flag' + str + 'Use']) {
				this._varsCakeBar['flag' + str + 'Now'] = this.vars.varsStatus['flag' + str + 'Now'];
			}
		}
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
	_getCakeVars : function(obj)
	{
		var insCurrent = obj.insReturn;
		if(obj.data) {
			insCurrent._getCakeVarsUpdate({
				arr  : insCurrent.vars.varsDetail,
				data : obj.data
			});
		}
	},

	/**
	 *
	*/
	_getCakeVarsUpdate : function(obj)
	{
		if (!this.vars.varsStatus.flagCakeUse) return;



		var str;
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].flagUse) continue;
			str = 'flagFoldNow' + obj.arr[i].id;
			obj.arr[i].flagFoldNow = (obj.data[str])? 1 : 0;
			this._getCakeVarsUpdate({
				arr  : obj.arr[i].child,
				data : obj.data
			});
		}
	},

	setCake : function(obj)
	{
		if (!this.insRoot.insCake) return;
		this._varsCake = {};
		this._setCakeVars({arr : obj.arr});
		this.insRoot.insCake.setStorageCake({
			parentKey  : this.idSelf,
			value      : this._varsCake,
			numExpires : 0
		});
	},

	/**
	 *
	*/
	_setCakeVars : function(obj)
	{
		if (!this.vars.varsStatus.flagCakeUse) return;
		var str;
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].flagUse) continue;
			if (this.vars.varsStatus.flagFoldUse) {
				str = 'flagFoldNow' + obj.arr[i].id;
				this._varsCake[str] = (obj.arr[i].flagFoldNow) ? 1 : 0;
			}
			this._setCakeVars({arr : obj.arr[i].child});
		}
	},

	/**
	 *
	*/
	_iniBtnBottom : function()
	{
		if (!this.vars.varsStatus.flagBtnBottomUse) return;
		if (this.vars.varsStatus.flagInnerBtnBottomUse) {
			this.eleInsertBtnRight = (this.insFormat.eleTemplate.fooder.down('.codeLibBaseMarginRightFive', 0))?
									  this.insFormat.eleTemplate.fooder.down('.codeLibBaseMarginRightFive', 0)
									: null;
			this.eleInsertBtnLeft = (this.insFormat.eleTemplate.fooder.down('.codeLibBaseMarginLeftFive', 0))?
									  this.insFormat.eleTemplate.fooder.down('.codeLibBaseMarginLeftFive', 0)
									: null;
		}
		this._extBtnBottom();
	},

	/**
	 *
	*/
	_mouseoverTitle : function(obj)
	{
		$(obj.vars.id).down('.codeLibTreeTitle', 0).addClassName('codeLibTreeTitleOver');
	},

	/**
	 *
	*/
	_mouseoutTitle : function(obj)
	{
		$(obj.vars.id).down('.codeLibTreeTitle', 0).removeClassName('codeLibTreeTitleOver');
	}

});
<?php }
}
?>