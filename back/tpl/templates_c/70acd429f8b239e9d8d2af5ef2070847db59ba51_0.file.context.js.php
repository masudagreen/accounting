<?php /* Smarty version 3.1.24, created on 2016-08-20 07:30:13
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/context.js" */ ?>
<?php
/*%%SmartyHeaderCode:126255278357b807053fdf63_16975625%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '70acd429f8b239e9d8d2af5ef2070847db59ba51' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/context.js',
      1 => 1471523676,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '126255278357b807053fdf63_16975625',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_57b80705422ec4_98173225',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_57b80705422ec4_98173225')) {
function content_57b80705422ec4_98173225 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '126255278357b807053fdf63_16975625';
?>

/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_Context = Class.create(Code_Lib_ExtLib,
{

	initialize : function(obj)
	{
		this._extAllot(obj);
		this._iniVars(obj);
		this._iniListener();
		this._iniWrap();
		this._iniList();
		this._setWrapStyle();
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
	},

	/**
	 *
	*/
	getVars : function()
	{
		this.allot({
			insCurrent : this.insCurrent,
			from       : 'getVars',
			vars       : this.vars.varsDetail
		});
	},

	/**
	 * Lock
	*/
	_iniLock : function(obj)
	{
		this._setLock();
	},

	/**
	 *
	*/
	insLock : null,
	_setLock : function() {
		this.insRoot.vars.varsSystem.num.zIndex ++;
		this.insLock = new Code_Lib_LockTemp();
		this.insLock.iniLoad({
			idSelf     : this.idSelf + 'Lock',
			idInsert   : this.insRoot.vars.varsSystem.id.root,
			numZIndex  : this.insRoot.vars.varsSystem.num.zIndex,
			insCurrent : this.insSelf,
			strFunc    : 'removeWrap'
		});
	},

	/**
	 * Wrap
	*/
	eleWrap : null,
	_iniWrap : function()
	{
		this._iniLock();
		this._setWrap();
	},

	_setWrap : function()
	{
		var ele = $(document.createElement('div'));
		this.eleInsert.insert(ele);
		ele.id = this.idSelf;
		ele.addClassName('codeLibContextWrap');
		this.eleWrap = ele;
	},



	/**
	 *
	*/
	_setWrapStyle : function()
	{
		this.eleWrap.setStyle({
			zIndex : this.insRoot.setZIndex(),
			top    : this.vars.varsStatus.numTop + 'px',
			left   : this.vars.varsStatus.numLeft + 'px'
		});
	},

	/**
	 *
	*/
	removeWrap : function()
	{
		if(this.insLock.eleLock) this.insLock.eleLock.remove();
		this.stopListener();
		this.eleWrap.remove();
		this.getVars();
	},

	/**
	 *
	*/
	_staticDisplay : {numNormal : 6, numMenu : 6, numMargin : 5},
	_iniDisplay : function(obj)
	{
		this._setDisplay(obj);
	},

	/**
	 *
	*/
	_setDisplay : function(obj)
	{
		var insTemplate = new Code_Lib_Template();
		var numWidth = obj.listWidth + this._staticDisplay.numNormal
								+ this._staticDisplay.numMenu + this._staticDisplay.numMargin;
		var numHeight = obj.listHeight + this._staticDisplay.numNormal
							 + this._staticDisplay.numMenu + this._staticDisplay.numMargin;
		var dataSha = insTemplate.get({
			flagType  : 'menuBox',
			numWidth  : numWidth,
			numHeight : numHeight,
			id        : ''
		});
		obj.eleParent.insert(dataSha);
		var dataNor = insTemplate.get({
			flagType  : 'normalBox',
			numWidth  : numWidth - this._staticDisplay.numMenu,
			numHeight : numHeight - this._staticDisplay.numMenu,
			id        : ''
		});
		obj.eleParent.down('.codeLibTemplateMenuBoxMiddleMiddle', 0).insert(dataNor);
		obj.eleParent.down('.codeLibTemplateNormalBoxMiddleMiddle', 0).addClassName('codeLibBaseBgFff');
		obj.eleParent.down('.codeLibTemplateNormalBoxMiddleMiddle', 0).insert(obj.eleChild);
	},

	/**
	 *
	*/
	_varsList : {numZIndex : 0},
	_staticList : {idle : 13},
	_iniList : function() {
		this._setList({
			idParent : this.idSelf + 'List-',
			arr      : this.vars.varsDetail
		});
		this._setListStyle({
			numTop  : 0,
			numLeft : 0,
			arr     : this.vars.varsDetail
		});
		this._setListHide({
			idParent : this.idSelf + 'List-',
			arr : this.vars.varsDetail
		});
	},

	/**
	 *
	*/
	_setList : function(obj)
	{
		var eleList = $(document.createElement('div'));
		eleList.id = obj.idParent + 'Wrap';
		eleList.addClassName('codeLibContextListWrap');
		var eleLineWrap = $(document.createElement('div'));
		eleLineWrap.addClassName('codeLibBaseMarginFive');
		$(this.insRoot.vars.varsSystem.id.root).insert(eleLineWrap);
		var style = this._iniLine({
			ele      : eleLineWrap,
			arr      : obj.arr,
			idParent : obj.idParent
		});
		this._iniDisplay({
			eleParent  : eleList,
			eleChild   : eleLineWrap,
			listWidth  : style.listWidth,
			listHeight : style.listHeight
		});
		this.eleWrap.insert(eleList);
	},

	/**
	 *
	*/
	_setListStyle : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].child.length) {
				this._varsList.numZIndex++;
				var numTop = obj.numTop + $(this.idSelf + 'List-'
									 + obj.arr[i].id).down('.codeLibContextLineArrow',0).offsetTop
									 - this._staticList.numIdle;
				var numLeft = obj.numLeft + $(this.idSelf + 'List-'
									 + obj.arr[i].id).down('.codeLibContextLineArrow',0).offsetLeft
									 + this._staticList.numIdle;
				$(this.idSelf + 'List-' + obj.arr[i].id + 'Wrap').setStyle({
					zIndex : this.insRoot.vars.varsSystem.num.zIndex + this._varsList.numZIndex,
					top    : numTop + 'px',
					left   : numLeft + 'px'
				});
				this._setListStyle({
					numTop  : numTop,
					numLeft : numLeft,
					arr     : obj.arr[i].child
				});
			}
		}
	},

	/**
	 *
	*/
	eleLineWrap : null,
	_staticLine : {numIdle : 16, numBlock : 16, numMargin : 5, numLength : 100},
	_iniLine : function(obj)
	{
		var style = this._setLine({
			idParent : obj.idParent,
			ele      : obj.ele,
			arr      : obj.arr
		});
		this._setLineListener({arr : obj.arr});

		return style;
	},

	/**
	 *
	*/
	_setLine : function(obj)
	{
		var listWidth = 0;
		var listHeight = 0;
		var lineWidth = 0;
		for (var i = 0; i < obj.arr.length; i++) {

			var eleWrap = $(document.createElement('span'));
			eleWrap.id = this.idSelf + 'List-' + obj.arr[i].id;
			eleWrap.unselectable = 'on';
			eleWrap.addClassName('codeLibContextLine');
			eleWrap.addClassName('unselect');
			if (obj.arr[i].flagCheckUse) eleWrap.addClassName('codeLibBaseCursorPointer');
			else eleWrap.addClassName('codeLibBaseCursorDefault');
			eleWrap.addClassName('clearfix');
			this.eleWrap.insert(eleWrap);

			var eleCheck = $(document.createElement('span'));
			eleCheck.addClassName('codeLibContextLineBlock');
			eleCheck.addClassName('codeLibContextLineCheck');

			if (obj.arr[i].flagCheckNow) {
				eleCheck.addClassName('codeLibContextLineChecked');
			}
			eleWrap.insert(eleCheck);

			var eleImg = $(document.createElement('span'));
			eleImg.addClassName('codeLibContextLineBlock');
			eleImg.addClassName('codeLibContextLineImg');
			if (obj.arr[i].strClass) {
				eleImg.addClassName(obj.arr[i].strClass);
			}
			eleWrap.insert(eleImg);

			var strTitle = '';
			if (obj.arr[i].strTitle.length > this._staticLine.numLength) {
				strTitle = obj.arr[i].strTitle.slice(0, this._staticLine.numLength);
			} else strTitle = obj.arr[i].strTitle;

			var eleTitle = $(document.createElement('span'));
			eleTitle.addClassName('codeLibContextLineTitle');
			eleTitle.insert(strTitle);
			eleTitle.title = obj.arr[i].strTitle;

			if (!obj.arr[i].flagCheckUse && !obj.arr[i].strClassFont) eleTitle.addClassName('codeLibContextLineTitleNoactive');
			eleWrap.insert(eleTitle);
			if (obj.arr[i].strClassFont) eleTitle.addClassName(obj.arr[i].strClassFont);

			var eleArrow = $(document.createElement('span'));
			eleArrow.addClassName('codeLibContextLineBlock');
			eleArrow.addClassName('codeLibContextLineArrow');
			if (obj.arr[i].child.length) {
				eleArrow.addClassName('codeLibContextLineArrowed');
				this._setList({
					idParent : eleWrap.id,
					arr      : obj.arr[i].child
				});
			}
			eleWrap.insert(eleArrow);

			var width = eleTitle.offsetWidth + this._staticLine.numIdle;

			if (width > lineWidth) lineWidth = width;

			width = width + this._staticLine.numBlock * 3 + this._staticLine.numMargin * 2;

			if (width > listWidth) listWidth = width;

			listHeight += eleTitle.offsetHeight;
			obj.ele.insert(eleWrap);

		}
		this._setLineStyle({
			arr       : obj.arr,
			listWidth : listWidth,
			lineWidth : lineWidth
		});
		var data = {
			listWidth  : listWidth,
			listHeight : listHeight
		};

		return data;
	},

	/**
	 *
	*/
	_setLineStyle : function(obj) {
		for (var i = 0; i < obj.arr.length; i++) {
			$(this.idSelf + 'List-' + obj.arr[i].id).setStyle({
				width : obj.listWidth + 'px'
			});
			$(this.idSelf + 'List-' + obj.arr[i].id).down('.codeLibContextLineTitle',0).setStyle({
				width : obj.lineWidth + 'px'
			});
		}
	},

	/**
	 *
	*/
	_updateLineVar : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == obj.vars.id) {
				if (obj.arr[i].flagCheckNow) obj.arr[i].flagCheckNow = 0;
				else  obj.arr[i].flagCheckNow = 1;
			}
		}

		return obj.arr;
	},

	/**
	 *
	*/
	_setLineListener : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			this.insListener.set({ bindAsEvent : 1, insCurrent : this, event : 'mousedown',strFunc : '_mousedownLine',
				ele : $(this.idSelf + 'List-' + obj.arr[i].id), vars : {vars : obj.arr[i]}
			});
			this.insListener.set({ bindAsEvent : 0, insCurrent : this, event : 'mouseover',strFunc : '_mouseoverLine',
				ele : $(this.idSelf + 'List-' + obj.arr[i].id), vars : {vars : obj.arr[i]}
			});
			this.insListener.set({ bindAsEvent : 0, insCurrent : this, event : 'mouseout',strFunc : '_mouseoutLine',
				ele : $(this.idSelf + 'List-' + obj.arr[i].id), vars : {vars : obj.arr[i]}
			});
		}
	},

	/**
	 *
	*/
	_mousedownLine : function(evt,obj)
	{
		if (obj) evt.stop();
		else obj = evt;
		if (!obj.vars.flagCheckUse) return;
		this._updateLineVar({
			arr  : this.vars.varsDetail,
			vars : obj.vars
		});
		this.allot({
			insCurrent : this.insCurrent,
			vars       : obj.vars,
			from       : '_mousedownLine'
		});
		this.removeWrap();
	},

	/**
	 *
	*/
	_mouseoverLine : function(obj)
	{
		this._setListHide({
			idParent : this.idSelf + 'List-',
			arr      : this.vars.varsDetail
		});
		if (!obj.vars.flagCheckUse) return;
		this._setListShow({vars : obj.vars});
		this._setListOver({vars : obj.vars});
	},

	/**
	 *
	*/
	_setListHide : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.idParent == this.idSelf + 'List-') $(obj.idParent + 'Wrap').show();
			else $(obj.idParent + 'Wrap').hide();
			if (obj.arr[i].child.length) {
				$(this.idSelf + 'List-' + obj.arr[i].id).removeClassName('codeLibContextLineOver');
				this._setListHide({
					idParent : this.idSelf + 'List-' + obj.arr[i].id,
					arr      : obj.arr[i].child
				});
			}
		}
	},

	/**
	 *
	*/
	_setListShow : function(obj)
	{
		var id = '';
		id += obj.vars.id;
		var array = id.split('-');
		var level='';
		var num = array.length;
		for (var i = 0; i < num; i++) {
			level += '-' + array[i];
			var id = this.idSelf + 'List' + level + 'Wrap';
			if ($(id)) $(id).show();
		}
	},

	/**
	 *
	*/
	_setListOver : function(obj)
	{
		var id = '';
		id += obj.vars.id;
		var array = id.split('-');
		var level = '';
		var num = array.length;
		for (var i = 0; i < num; i++) {
			level += '-' + array[i];
			var id = this.idSelf + 'List' + level;
			if ($(id)) $(id).addClassName('codeLibContextLineOver');
		}
	},

	/**
	 *
	*/
	_mouseoutLine : function(obj)
	{
		if (!obj.vars.flagCheckUse) return;
		$(this.idSelf + 'List-'+ obj.vars.id).removeClassName('codeLibContextLineOver');
	}
});

<?php }
}
?>