<?php /* Smarty version 3.1.24, created on 2016-08-18 12:50:22
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/layout.js" */ ?>
<?php
/*%%SmartyHeaderCode:179146044857b5af0e3b0151_30455103%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '22087bbe71ba9fccf7ec3c0f3392e30577a4fa99' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/layout.js',
      1 => 1471523676,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '179146044857b5af0e3b0151_30455103',
  'variables' => 
  array (
    'varsLoad' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_57b5af0e48aaf6_83078549',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_57b5af0e48aaf6_83078549')) {
function content_57b5af0e48aaf6_83078549 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '179146044857b5af0e3b0151_30455103';
?>

/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_Layout = Class.create(Code_Lib_ExtLib,
{

	varsLoad : <?php echo $_smarty_tpl->tpl_vars['varsLoad']->value;?>
,


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
		this._iniResize();
		this._iniMove();
		this._iniSwitch();
	},

	/**
	 *
	*/
	iniReload : function()
	{
		this.stopListener();
		this._removeResize();
		this._varTemplateWrap();
		this._varTemplate({arr : this.vars.varsDetail});
		this._varTemplateBox({arr : this.vars.varsDetail});
		this._styleTemplateUpdate({arr : this.vars.varsDetail});
		this._iniResize();
		this._templateMoveListener({arr : this.vars.varsDetail});
		this._templateSwitchListener({arr : this.vars.varsDetail});

	},

	/**
	 *
	*/
	_iniVars : function(obj)
	{
		this._extVars(obj);
		this._iniCake();
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
		if (!this.vars.varsStatus.flagCakeUse) return;
		obj.arr = this.vars.varsDetail;
		var str = 'flagNow';
		if(obj.data[str]) this.vars.varsStatus.flagNow = obj.data[str];
		str = 'flagSwitchNow';
		if(obj.data[str]) this.vars.varsStatus.flagSwitchNow = obj.data[str];

		for (var i = 0; i < obj.arr.length; i++) {
			for (var i = 0; i < obj.arr.length; i++) {
				if (this.vars.varsStatus.flagMoveUse) {
					var str = 'numSort' + obj.arr[i].id;
					obj.arr[i].numSort = obj.data[str];
				}
				if (this.vars.varsStatus.flagResizeUse) {
					var str = 'numWidth' + obj.arr[i].id;
					obj.arr[i].numWidth = obj.data[str];
					var str = 'numHeight' + obj.arr[i].id;
					obj.arr[i].numHeight = obj.data[str];
				}
				if (this.vars.varsStatus.flagSwitchUse) {
					var array = this._staticCake;
					for (var j = 0; j < array.length; j++) {
						var str = 'switch' + array[j] + obj.arr[i].id;
						obj.arr[i][array[j]] = obj.data[str];
					}
				}
			}

		}
	},

	/**
	 *
	*/
	_staticCake : [
		'numHeightStandard', 'numWidthStandard',
		'numHeightWide', 'numWidthWide',
		'numHeightClassic', 'numWidthClassic'
	],
	_setCakeVars : function(obj)
	{
		if (!this.vars.varsStatus.flagCakeUse) return;
		var obj = {};
		obj.arr = this.vars.varsDetail;
		this._varsCake['flagNow'] = this.vars.varsStatus.flagNow;
		this._varsCake['flagSwitchNow'] = this.vars.varsStatus.flagSwitchNow;
		for (var i = 0; i < obj.arr.length; i++) {
			if (this.vars.varsStatus.flagMoveUse) {
				var str = 'numSort' + obj.arr[i].id;
				this._varsCake[str] = obj.arr[i].numSort;
			}
			if (this.vars.varsStatus.flagResizeUse) {
				var str = 'numWidth' + obj.arr[i].id;
				this._varsCake[str] = obj.arr[i].numWidth;
				var str = 'numHeight' + obj.arr[i].id;
				this._varsCake[str] = obj.arr[i].numHeight;
			}
			if (this.vars.varsStatus.flagSwitchUse) {
				var array = this._staticCake;
				for (var j = 0; j < array.length; j++) {
					var str = 'switch' + array[j] + obj.arr[i].id;
					this._varsCake[str] = obj.arr[i][array[j]];
				}
			}
		}
	},


	/**
	 * Wrap
	*/
	_iniWrap : function()
	{
		var ele = $(document.createElement('div'));
		ele.id = this.idSelf;
		ele.addClassName('clearfix');
		ele.addClassName('codeLibLayoutWrap');
		this.eleInsert.insert(ele);
		this.eleInsert.addClassName('codeLibLayoutBg');
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
	_staticTemplate : {
		numPadding : 2, numBoxLeftWidth : 3, numBoxHeight : 40,
		numLayoutTopHeight : 8, numLayoutBottomHeight : 6, numLayoutLeftWidth : 6
	},
	_iniTemplate : function()
	{
		this._varTemplateWrap();
		this._varTemplate({arr : this.vars.varsDetail});
		this._setTemplate({arr : this.vars.varsDetail});
		this._varTemplateBox({arr : this.vars.varsDetail});
		this._setTemplateBox({arr : this.vars.varsDetail});
	},

	/**
	 *
	*/
	_errTemplate : function(obj)
	{
		alert('template vars error ' + obj.flag);
	},


	/**
	 *
	*/
	_varTemplate : function(obj)
	{

		obj.arr = obj.arr.sortBy(function(v, i) {
			return v.numSort;
		});
		for (var i = 0; i < obj.arr.length; i++) {
			obj.arr[i].numSort = i;
		}
		obj.arr = obj.arr.sortBy(function(v, i) {
			return v.numPriority;
		});
		for (var i = 0; i < obj.arr.length; i++) {
			obj.arr[i].numPriority = i;
		}
		var flagSwitcNow = this.vars.varsStatus.flagSwitchNow;
		if (this.vars.varsStatus.flagNow == 1) {
			var numHeight = this._varsTemplateWrap.numHeight
						- (this._staticTemplate.numLayoutTopHeight + this._staticTemplate.numLayoutBottomHeight);
			var numWidth = this._varsTemplateWrap.numWidth
						- (this._staticTemplate.numLayoutLeftWidth * 2);
			for (var i = 0; i < obj.arr.length; i++) {
				obj.arr[i].numHeight = numHeight;
				obj.arr[i].numWidth = numWidth;
			}
		} else if (this.vars.varsStatus.flagNow == 2) {
			if (flagSwitcNow == 'Standard') {
				var numHeight = this._varsTemplateWrap.numHeight
							- (this._staticTemplate.numLayoutTopHeight + this._staticTemplate.numLayoutBottomHeight);
				var numWidth = this._varsTemplateWrap.numWidth
							- (this._staticTemplate.numLayoutLeftWidth * 2) * 2;
				var numWidthMin = obj.arr[0].numWidthMin + obj.arr[1].numWidthMin;
				if(numWidthMin > numWidth) {
					this._errTemplate({
						flag : flagSwitcNow + ' = ' + (numWidthMin - numWidth)
					});
				}
				for (var i = 0; i < obj.arr.length; i++) {
					/*height*/
					obj.arr[i].numHeight = numHeight;

					/*width*/
					if (i == 0) {
						obj.arr[0].numWidth = obj.arr[0].numWidth;
						obj.arr[1].numWidth = numWidth - obj.arr[0].numWidth;
						if(obj.arr[1].numWidth < obj.arr[1].numWidthMin){
							obj.arr[1].numWidth = obj.arr[1].numWidthMin;
							obj.arr[0].numWidth = numWidth - obj.arr[1].numWidth;
						}
					}
					obj.arr[i]['numHeight' + flagSwitcNow] = numHeight;
					obj.arr[i]['numWidth' + flagSwitcNow] = numWidth;
				}
			} else if (flagSwitcNow == 'Wide') {
				var numHeight = this._varsTemplateWrap.numHeight
							- (this._staticTemplate.numLayoutTopHeight
							+ this._staticTemplate.numLayoutBottomHeight) * 2;
				var numWidth = this._varsTemplateWrap.numWidth
							- (this._staticTemplate.numLayoutLeftWidth * 2);
				var numHeightMin = obj.arr[0].numHeightMin + obj.arr[1].numHeightMin;
				if(numHeightMin > numHeight) {
					this._errTemplate({
						flag : flagSwitcNow + ' = ' + (numHeightMin - numHeight)
					});
				}
				for (var i = 0; i < obj.arr.length; i++) {
					/*width*/
					obj.arr[i].numWidth = numWidth;
					/*height*/
					if (i == 0) {
						obj.arr[0].numHeight = obj.arr[0].numHeight;
						obj.arr[1].numHeight = numHeight - obj.arr[0].numHeight;
						if(obj.arr[1].numHeight < obj.arr[1].numHeightMin){
							obj.arr[1].numHeight = obj.arr[1].numHeightMin;
							obj.arr[0].numHeight = numHeight - obj.arr[1].numHeight;
						}
					}
					obj.arr[i]['numHeight' + flagSwitcNow] = numHeight;
					obj.arr[i]['numWidth' + flagSwitcNow] = numWidth;
				}
			}
		} else if (this.vars.varsStatus.flagNow == 3) {
			if (flagSwitcNow == 'Standard') {
				var numHeight = this._varsTemplateWrap.numHeight
							- (this._staticTemplate.numLayoutTopHeight + this._staticTemplate.numLayoutBottomHeight);
				var numWidth = this._varsTemplateWrap.numWidth
							- (this._staticTemplate.numLayoutLeftWidth * 2) * 3;
				var numWidthMin = obj.arr[0].numWidthMin + obj.arr[1].numWidthMin + obj.arr[2].numWidthMin;
				if(numWidthMin > numWidth) {
					this._errTemplate({
						flag : flagSwitcNow + ' = ' + (numWidthMin - numWidth)
					});
				}
				for (var i = 0; i < obj.arr.length; i++) {
					/*height*/
					obj.arr[i].numHeight = numHeight;
					/*width*/
					if (i == 0) {
						obj.arr[0].numWidth = obj.arr[0].numWidth;
						obj.arr[1].numWidth = obj.arr[1].numWidth;
						obj.arr[2].numWidth = numWidth - obj.arr[0].numWidth - obj.arr[1].numWidth;
						if (obj.arr[2].numWidth < obj.arr[2].numWidthMin) {
							obj.arr[2].numWidth = obj.arr[2].numWidthMin;
							obj.arr[1].numWidth = numWidth - obj.arr[0].numWidth - obj.arr[2].numWidth;
							if (obj.arr[1].numWidth < obj.arr[1].numWidthMin) {
								obj.arr[1].numWidth = obj.arr[1].numWidthMin;
								obj.arr[0].numWidth = numWidth - obj.arr[1].numWidth - obj.arr[2].numWidth;
							}
						}
					}
				}
			} else if (flagSwitcNow == 'Wide') {
				obj.arr = obj.arr.sortBy(function(v, i) {
					return v.numSort;
				});
				var numHeightSpan =
					(this._staticTemplate.numLayoutTopHeight + this._staticTemplate.numLayoutBottomHeight) * 2;
				var numWidthSpan = (this._staticTemplate.numLayoutLeftWidth * 2);
				var numHeight = this._varsTemplateWrap.numHeight - numHeightSpan;
				var numHeightRight = 0;
				var numWidth = this._varsTemplateWrap.numWidth - numWidthSpan;
				var numWidthBottom = numWidth;
				obj.arr[2].numWidth = numWidthBottom;
				numWidth -= numWidthSpan;
				var numWidthMin = obj.arr[0].numWidthMin + obj.arr[1].numWidthMin;
				if(numWidthMin > numWidth || obj.arr[2].numWidthMin > numWidth) {
					this._errTemplate({
						flag : flagSwitcNow + ' = Width'
					});
				}
				/*width*/
				obj.arr = obj.arr.sortBy(function(v, i) {
					return v.numPriority;
				});
				var numFirst = null;
				var numSecond = null;
				var numBottom = null;
				var flagPriorityBottom = 0;
				for (var i = 0; i < obj.arr.length; i++) {
					if(obj.arr[i].numSort == 2) {
						numBottom = i;
						if(obj.arr[i].numPriority == 0) flagPriorityBottom = 1;
						continue;
					}
					if(numFirst == null) numFirst = i;
					else numSecond = i;
				}
				obj.arr[numFirst].numWidth = obj.arr[numFirst].numWidth;
				obj.arr[numSecond].numWidth = numWidth - obj.arr[numFirst].numWidth;
				if(obj.arr[numSecond].numWidth < obj.arr[numSecond].numWidthMin){
					obj.arr[numSecond].numWidth = obj.arr[numSecond].numWidthMin;
					obj.arr[numFirst].numWidth = numWidth - obj.arr[numSecond].numWidth;
				}
				/*height*/
				var numTop;
				if (obj.arr[numFirst].numHeightMin > obj.arr[numSecond].numHeightMin) {
					numTop = numFirst;
					obj.arr[numSecond].numHeightMin = obj.arr[numFirst].numHeightMin;
				} else {
					numTop = numSecond;
					obj.arr[numFirst].numHeightMin = obj.arr[numSecond].numHeightMin;
				}
				var numHeightMin = obj.arr[numTop].numHeightMin + obj.arr[numBottom].numHeightMin;
				if(numHeightMin > numHeight) {
					this._errTemplate({
						flag : flagSwitcNow + ' = ' + (numHeightMin - numHeight)
					});
				}
				if (flagPriorityBottom) {
					obj.arr[numBottom].numHeight = obj.arr[numBottom].numHeight;
					obj.arr[numFirst].numHeight = numHeight - obj.arr[numBottom].numHeight;
					obj.arr[numSecond].numHeight = numHeight - obj.arr[numBottom].numHeight;
					if(obj.arr[numTop].numHeight < obj.arr[numTop].numHeightMin){
						obj.arr[numFirst].numHeight = obj.arr[numTop].numHeightMin;
						obj.arr[numSecond].numHeight = obj.arr[numTop].numHeightMin;
						obj.arr[numBottom].numHeight = numHeight - obj.arr[numTop].numHeight;
					}
				} else {
					obj.arr[numFirst].numHeight = obj.arr[numTop].numHeight;
					obj.arr[numSecond].numHeight = obj.arr[numTop].numHeight;
					obj.arr[numBottom].numHeight = numHeight - obj.arr[numTop].numHeight;
					if(obj.arr[numBottom].numHeight < obj.arr[numBottom].numHeightMin){
						obj.arr[numBottom].numHeight = obj.arr[numBottom].numHeight;
						obj.arr[numFirst].numHeight = numHeight - obj.arr[numBottom].numHeightMin;
						obj.arr[numSecond].numHeight = numHeight - obj.arr[numBottom].numHeightMin;
					}
				}
			} else if (flagSwitcNow == 'Classic') {
				obj.arr = obj.arr.sortBy(function(v, i) {
					return v.numSort;
				});
				var numHeightSpan = this._staticTemplate.numLayoutTopHeight
								+ this._staticTemplate.numLayoutBottomHeight;
				var numWidthSpan = ( this._staticTemplate.numLayoutLeftWidth * 2 ) * 2;
				var numHeight = this._varsTemplateWrap.numHeight - numHeightSpan;
				var numWidth = this._varsTemplateWrap.numWidth - numWidthSpan;
				obj.arr[0].numHeight = numHeight;
				numHeight -= numHeightSpan;
				var numHeightMin = obj.arr[1].numHeightMin + obj.arr[2].numHeightMin;
				if(numHeightMin > numHeight || obj.arr[0].numHeightMin > numHeight) {
					this._errTemplate({
						flag : flagSwitcNow + ' = Height'
					});
				}
				/*height*/
				obj.arr = obj.arr.sortBy(function(v, i) {
					return v.numPriority;
				});
				var numFirst = null;
				var numSecond = null;
				var numLeft = null;
				var flagPriorityLeft = 0;
				for (var i = 0; i < obj.arr.length; i++) {
					if(obj.arr[i].numSort == 0) {
						numLeft = i;
						if(obj.arr[i].numPriority == 0) flagPriorityLeft = 1;
						continue;
					}
					if(numFirst == null) numFirst = i;
					else numSecond = i;
				}
				obj.arr[numFirst].numHeight = obj.arr[numFirst].numHeight;
				obj.arr[numSecond].numHeight = numHeight - obj.arr[numFirst].numHeight;
				if(obj.arr[numSecond].numHeight < obj.arr[numSecond].numHeightMin){
					obj.arr[numSecond].numHeight = obj.arr[numSecond].numHeightMin;
					obj.arr[numFirst].numHeight = numHeight - obj.arr[numSecond].numHeight;
				}
				/*width*/
				var numRight;
				if (obj.arr[numFirst].numWidthMin > obj.arr[numSecond].numWidthMin) {
					numRight = numFirst;
					obj.arr[numSecond].numWidthMin = obj.arr[numFirst].numWidthMin;
				} else {
					numRight = numSecond;
					obj.arr[numFirst].numWidthMin = obj.arr[numSecond].numWidthMin;
				}
				var numWidthMin = obj.arr[numRight].numWidthMin + obj.arr[numLeft].numWidthMin;
				if(numWidthMin > numWidth) {
					this._errTemplate({
						flag : flagSwitcNow + ' = ' + (numWidthMin - numWidth)
					});
				}
				if (flagPriorityLeft) {
					obj.arr[numLeft].numWidth = obj.arr[numLeft].numWidth;
					obj.arr[numFirst].numWidth = numWidth - obj.arr[numLeft].numWidth;
					obj.arr[numSecond].numWidth = numWidth - obj.arr[numLeft].numWidth;
					if(obj.arr[numRight].numWidth < obj.arr[numRight].numWidthMin){
						obj.arr[numFirst].numWidth = obj.arr[numRight].numWidthMin;
						obj.arr[numSecond].numWidth = obj.arr[numRight].numWidthMin;
						obj.arr[numLeft].numWidth = numWidth - obj.arr[numRight].numWidth;
					}
				} else {
					obj.arr[numFirst].numWidth = obj.arr[numRight].numWidth;
					obj.arr[numSecond].numWidth = obj.arr[numRight].numWidth;
					obj.arr[numLeft].numWidth = numWidth - obj.arr[numRight].numWidth;
					if(obj.arr[numLeft].numWidth < obj.arr[numLeft].numWidthMin){
						obj.arr[numLeft].numWidth = obj.arr[numLeft].numWidth;
						obj.arr[numFirst].numWidth = numWidth - obj.arr[numLeft].numWidthMin;
						obj.arr[numSecond].numWidth = numWidth - obj.arr[numLeft].numWidthMin;
					}
				}
			}
		}
		this.vars.varsDetail = obj.arr.sortBy(function(v, i) {
			return v.numSort;
		});
	},

	/**
	 *
	*/
	_varsTemplateWrap : {numHeight : 0, numWidth : 0},
	_varTemplateWrap : function()
	{
		var array = this.eleInsert.style.height.split('px');
		this._varsTemplateWrap.numHeight = parseFloat(array[0]) - this._staticTemplate.numPadding * 2;

		array = this.eleInsert.style.width.split('px');
		this._varsTemplateWrap.numWidth = parseFloat(array[0]) - this._staticTemplate.numPadding * 2;
	},

	/**
	 *
	*/
	_setTemplate : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			var insTemplate = new Code_Lib_Template();
			var data = insTemplate.get({
				flagType  : 'shadowBox',
				numWidth  : obj.arr[i].numWidth,
				numHeight : obj.arr[i].numHeight,
				id        : (this.idSelf + obj.arr[i].id)
			});
			this.eleInsert.down('.codeLibLayoutWrap',0).insert(data);
		}
	},

	/**
	 *
	*/
	_varTemplateBox : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].flagBoxUse) continue;
			obj.arr[i].numWidthBox =  obj.arr[i].numWidth - this._staticTemplate.numBoxLeftWidth * 2;
			obj.arr[i].numHeightBox =  this._staticTemplate.numBoxHeight;
		}
	},

	/**
	 *
	*/
	_setTemplateBox : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].flagBoxUse) continue;
			var insTemplate = new Code_Lib_Template();
			var data = insTemplate.get({
				flagType  : 'normalBox',
				numWidth  : obj.arr[i].numWidthBox,
				numHeight : obj.arr[i].numHeightBox,
				id        : (this.idSelf + obj.arr[i].id)+'Box'
			});
			var id = this.idSelf + obj.arr[i].id;
			$(id).down('.codeLibTemplateShadowBoxMiddleMiddle',0).insert(data);
			$(id).down('.codeLibTemplateNormalBoxMiddleMiddle',0).addClassName('codeLibLayoutBoxBg');
		}
	},

	/**
	 *
	*/
	_styleTemplateUpdate : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			var insTemplate = new Code_Lib_Template();
			insTemplate.updateStyle({
				flagType  : 'shadowBox',
				id        : (this.idSelf + obj.arr[i].id),
				numHeight : obj.arr[i].numHeight,
				numWidth  : obj.arr[i].numWidth
			});
			if (!obj.arr[i].flagBoxUse) continue;
			insTemplate.updateStyle({
				flagType  : 'normalBox',
				id        : (this.idSelf + obj.arr[i].id) + 'Box',
				numHeight : obj.arr[i].numHeightBox,
				numWidth  : obj.arr[i].numWidthBox
			});

		}
		for (var i = 0; i < obj.arr.length; i++) {
			this.eleInsert.down('.codeLibLayoutWrap',0).insert($((this.idSelf + obj.arr[i].id)));
		}
	},

	/**
	 * Switch
	*/
	_iniSwitch : function()
	{
		if (!this.vars.varsStatus.flagSwitchUse) return;
		this._templateSwitch();
		this._templateSwitchListener();
	},

	/**
	 *
	*/
	_templateSwitch : function()
	{
		var ele = $(document.createElement('li'));
		ele.addClassName('codeLibWindowNaviSwitch');
		ele.addClassName('codeLibBaseCursorPointer');
		ele.hide();
		ele.title = this.varsLoad.varsWhole.strSwitch;
		$(this.idWindow).down('.codeLibWindowNavi', 0).insert(ele);
		new Effect.Appear(ele);
	},

	/**
	 *
	*/
	_templateSwitchListener : function()
	{
		if (!this.vars.varsStatus.flagSwitchUse) return;
		this.insListener.set({
			bindAsEvent : 1, insCurrent : this, event : 'mousedown',
			strFunc : '_mousedownSwitch', ele : $(this.idWindow).down('.codeLibWindowNaviSwitch', 0),
			vars : ''
		});
		this.insListener.set({
			bindAsEvent : 0, insCurrent : this, event : 'mouseover',
			strFunc : '_mouseoverSwitch', ele : $(this.idWindow).down('.codeLibWindowNaviSwitch', 0),
			vars : ''
		});
		this.insListener.set({
			bindAsEvent : 0, insCurrent : this, event : 'mouseout',
			strFunc : '_mouseoutSwitch', ele : $(this.idWindow).down('.codeLibWindowNaviSwitch', 0),
			vars : ''
		});
	},

	/**
	 *
	*/
	_mouseoverSwitch : function()
	{
		var ele = $(this.idWindow).down('.codeLibWindowNaviSwitch', 0);
		ele.addClassName('codeLibWindowNaviSwitchOver');
	},

	/**
	 *
	*/
	_mouseoutSwitch : function()
	{
		var ele = $(this.idWindow).down('.codeLibWindowNaviSwitch', 0);
		ele.removeClassName('codeLibWindowNaviSwitchOver');
	},

	/**
	 *
	*/
	_varsSwitch : {},
	_mousedownSwitch : function(evt)
	{
		evt.stop();
		this._varsSwitch = {};
		this._varsSwitch = {
			flagPast   : null
		};
		this.allot({
			insCurrent : this.insCurrent,
			from       : '_preMousedownSwitch'
		});
		this._varSwitchNowUpdate({arr : this.vars.varsStatus.switchList});
		this._varSwitchUpdate({arr : this.vars.varsDetail});
		this.iniReload();
		this.setCake();
		this.allot({
			insCurrent : this.insCurrent,
			from       : '_mousedownSwitch'
		});
		this._varsSwitch = {};
	},

	/**
	 *
	*/
	_varSwitchUpdate : function(obj)
	{
		var strWidth = 'numWidth' + this.vars.varsStatus.flagSwitchNow;
		var strHeight = 'numHeight' + this.vars.varsStatus.flagSwitchNow;
		var strWidthPast = 'numWidth' + this._varsSwitch.flagPast;
		var strHeightPast = 'numHeight' + this._varsSwitch.flagPast;
		for (var i = 0; i < obj.arr.length; i++) {
			if (this.vars.varsStatus.flagNow != 1) {
				obj.arr[i][strWidthPast] = obj.arr[i].numWidth;
				obj.arr[i][strHeightPast] = obj.arr[i].numHeight;
				obj.arr[i].numWidth = obj.arr[i][strWidth];
				obj.arr[i].numHeight = obj.arr[i][strHeight];
			}
		}
	},

	/**
	 *
	*/
	_varSwitchNowUpdate : function(obj)
	{
		var flagPast = this.vars.varsStatus.flagSwitchNow;
		this._varsSwitch.flagPast = flagPast;

		var flag = '';
		for (var i = 0; i < obj.arr.length; i++) {
			if (this.vars.varsStatus.flagSwitchNow == obj.arr[i]) flag = i;
		}
		var num = obj.arr.length - 1;
		if (flag == num) flag = 0;
		else flag++;
		this.vars.varsStatus.flagSwitchNow = obj.arr[flag];

	},


	/**
	 * Resize
	*/
	_varsResize : {},
	_iniResize : function()
	{
		if (!this.vars.varsStatus.flagResizeUse) return;
		this._templateResize({arr : this.vars.varsDetail});
		this._templateResizeListener({arr : this.vars.varsDetail});
	},

	/**
	 *
	*/
	_removeResize : function()
	{
		for (var i = 0; i < this._varsResizeEle.length; i++) {
			this._varsResizeEle[i].remove();
		}
		this._varsResizeEle = [];
	},


	/**
	 *
	*/
	_varsResizeEle : [],
	_templateResize : function(obj)
	{
		if (!this.vars.varsStatus.flagResizeUse) return;
		this._varsResizeEle = [];
		if (this.vars.varsStatus.flagNow == 2) {
			var ele = $(document.createElement('div'));
			ele.addClassName('codeLibLayoutNavi');
			this._varsResizeEle.push(ele);
			for (var i = 0; i < obj.arr.length; i++) {
				if (i == 0) {
					if (this.vars.varsStatus.flagSwitchNow == 'Standard') {
						ele.addClassName('codeLibLayoutNaviXArea');
						ele.addClassName('codeLibBaseCursorE-resize');
						var id = this.idSelf + obj.arr[i].id;
						$(id).down('.codeLibTemplateShadowBoxMiddleRight',0).insert(ele);
					} else if (this.vars.varsStatus.flagSwitchNow == 'Wide') {
						ele.addClassName('codeLibLayoutNaviYArea');
						ele.addClassName('codeLibBaseCursorS-resize');
						var id = this.idSelf + obj.arr[i].id;
						$(id).down('.codeLibTemplateShadowBoxBottomMiddle',0).insert(ele);
					}
				}
			}
		} else if (this.vars.varsStatus.flagNow == 3) {
			var eleA = $(document.createElement('div'));
			eleA.addClassName('codeLibLayoutNavi');
			var eleB = $(document.createElement('div'));
			eleB.addClassName('codeLibLayoutNavi');
			this._varsResizeEle.push(eleA);
			this._varsResizeEle.push(eleB);
			for (var i = 0; i < obj.arr.length; i++) {
				if (i == 0) {
					eleA.addClassName('codeLibLayoutNaviXArea');
					eleA.addClassName('codeLibBaseCursorE-resize');
					var id = this.idSelf + obj.arr[i].id;
					$(id).down('.codeLibTemplateShadowBoxMiddleRight',0).insert(eleA);
				} else if (i == 1) {
					if (this.vars.varsStatus.flagSwitchNow == 'Standard') {
						eleB.addClassName('codeLibLayoutNaviXArea');
						eleB.addClassName('codeLibBaseCursorE-resize');
						var id = this.idSelf + obj.arr[i].id;
						$(id).down('.codeLibTemplateShadowBoxMiddleRight',0).insert(eleB);
					}
				} else if (i == 2) {
					if (this.vars.varsStatus.flagSwitchNow == 'Wide'
						|| this.vars.varsStatus.flagSwitchNow == 'Classic'
					) {
						eleB.addClassName('codeLibLayoutNaviYArea');
						eleB.addClassName('codeLibBaseCursorS-resize');
						var id = this.idSelf + obj.arr[i].id;
						$(id).down('.codeLibTemplateShadowBoxTopMiddle',0).insert(eleB);
					}
				}
			}
		}
	},

	/**
	 *
	*/
	_templateResizeListener : function(obj)
	{
		if (!this.vars.varsStatus.flagResizeUse) return;
		if (this.vars.varsStatus.flagNow == 2) {
			for (var i = 0; i < obj.arr.length; i++) {
				if (i == 0) {
					if (this.vars.varsStatus.flagSwitchNow == 'Standard') {
						this.insListener.set({
							bindAsEvent : 1, insCurrent : this, event : 'mousedown',
							strFunc : '_mousedownResize',
							ele : $((this.idSelf + obj.arr[i].id)).down('.codeLibLayoutNaviXArea',0),
							vars : {flagArrection : 0, sortThis : 0, sortThat : 1}
						});
					} else if (this.vars.varsStatus.flagSwitchNow == 'Wide') {
						this.insListener.set({
							bindAsEvent : 1, insCurrent : this, event : 'mousedown',
							strFunc : '_mousedownResize',
							ele : $((this.idSelf + obj.arr[i].id)).down('.codeLibLayoutNaviYArea',0),
							vars : {flagArrection : 0, sortThis : 0, sortThat : 1}
						});
					}
				}
			}
		} else if (this.vars.varsStatus.flagNow == 3) {
			for (var i = 0; i < obj.arr.length; i++) {
				if (i == 0) {
					this.insListener.set({
						bindAsEvent : 1, insCurrent : this, event : 'mousedown',
						strFunc : '_mousedownResize',
						ele : $((this.idSelf + obj.arr[i].id)).down('.codeLibLayoutNaviXArea',0),
						vars : {flagArrection : 0, sortThis : 0, sortThat : 1}
					});
				} else if (i == 1) {
					if (this.vars.varsStatus.flagSwitchNow == 'Standard') {
						this.insListener.set({
							bindAsEvent : 1, insCurrent : this, event : 'mousedown',
							strFunc : '_mousedownResize',
							ele : $((this.idSelf + obj.arr[i].id)).down('.codeLibLayoutNaviXArea',0),
							vars : {flagArrection : 1, sortThis : 1, sortThat : 2}
						});
					}
				} else if (i == 2) {
					if (this.vars.varsStatus.flagSwitchNow == 'Wide'
						|| this.vars.varsStatus.flagSwitchNow == 'Classic'
					) {
						this.insListener.set({
							bindAsEvent : 1, insCurrent : this, event : 'mousedown',
							strFunc : '_mousedownResize',
							ele : $((this.idSelf + obj.arr[i].id)).down('.codeLibLayoutNaviYArea',0),
							vars : {flagArrection : 1, sortThis : 1, sortThat : 2}
						});
					}
				}
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
	getResizeSort : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.num == i) return obj.arr[i];
		}
	},

	/**
	 *
	*/
	getResizeArrection : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.num == i) {
				if ($((this.idSelf + obj.arr[i].id)).down('.codeLibLayoutNaviXArea',0)) return 'x';
				else return 'y';
			}
		}
	},

	/**
	 *
	*/
	_staticResize : {
		numWindowHead : 48, numLayoutTopHeight : 8, numLayoutBottomHeight : 6,
		numLayoutLeftWidth : 6, numBoxLeftWidth : 3, numPadding : 2
	},

	/**
	 *
	*/
	_mousedownResize : function(evt, obj) {
		var array = (this.eleInsert.up('.codeLibWindow',0).style.top).split('px');
		var numDisplayTop = parseFloat(array[0]) + this._staticResize.numWindowHead;
		this._varsResize = {};
		this._varsResize = {
			flag             : 1,
			ele              : evt.element(),
			sortThis         : this.getResizeSort({arr : this.vars.varsDetail, num : obj.sortThis}),
			sortThat         : this.getResizeSort({arr : this.vars.varsDetail, num : obj.sortThat}),
			flagArrection    : this.getResizeArrection({arr : this.vars.varsDetail, num : obj.flagArrection}),
			numDisplayLeft   : this.eleInsert.up('.codeLibWindow',0).offsetLeft + this.eleInsert.offsetLeft,
			numDisplayTop    : numDisplayTop,
			numLayoutLeftMin : 0,
			numLayoutTopMin  : 0,
			numLayoutLeftMax : 0,
			numLayoutTopMax  : 0,
			numMouseLeft     : evt.pointerX(),
			numMouseTop      : evt.pointerY(),
			numNaviBarXLeft  : evt.pointerX(),
			numNaviBarXTop   : 0,
			numNaviBarYLeft  : 0,
			numNaviBarYTop   : evt.pointerY(),
			eleBarX          : null,
			eleBarY          : null,
			eleLock          : null
		};
		var omit = this._varsResize;
		if (this.vars.varsStatus.flagNow == 2) {
			if (this.vars.varsStatus.flagSwitchNow == 'Standard') {
				var numRight = omit.numDisplayLeft
						+ this.eleInsert.offsetWidth
						- this._staticResize.numPadding
						- this._staticResize.numLayoutLeftWidth;
				var numLeft = omit.numDisplayLeft
						+ $(this.idSelf
						+ omit.sortThis.id).down('.codeLibTemplateShadowBoxMiddleMiddle',0).offsetLeft;
				omit.numLayoutLeftMin = numLeft + omit.sortThis.numWidthMin;
				omit.numLayoutLeftMax = numRight - omit.sortThat.numWidthMin;
			} else if (this.vars.varsStatus.flagSwitchNow == 'Wide') {
				var numTop = omit.numDisplayTop + this._staticResize.numLayoutTopHeight;
				var bottom = omit.numDisplayTop
							+ this.eleInsert.offsetHeight
							- this._staticResize.numPadding
							- this._staticResize.numLayoutBottomHeight;
				omit.numLayoutTopMin = numTop + omit.sortThis.numHeightMin;
				omit.numLayoutTopMax = bottom - omit.sortThat.numHeightMin;
			}
		} else if (this.vars.varsStatus.flagNow == 3) {
			if (this.vars.varsStatus.flagSwitchNow == 'Standard') {
				var ele = $(this.idSelf + omit.sortThis.id).down('.codeLibTemplateShadowBoxMiddleMiddle',0);
				var numLeft = omit.numDisplayLeft + ele.offsetLeft;
				if (omit.sortThis.numSort == 0) {
					var idThis = this.idSelf + omit.sortThis.id;
					var idThat = this.idSelf + omit.sortThat.id;
					var eleThis = $(idThis).down('.codeLibTemplateShadowBoxMiddleMiddle',0);
					var eleThat = $(idThat).down('.codeLibTemplateShadowBoxMiddleMiddle',0);
					var numRight = omit.numDisplayLeft
							+ this._staticResize.numPadding
							+ eleThis.offsetWidth
							+ this._staticResize.numLayoutLeftWidth * 4
							+ eleThat.offsetWidth;
					omit.numLayoutLeftMin = numLeft + omit.sortThis.numWidthMin;
					omit.numLayoutLeftMax = numRight - omit.sortThat.numWidthMin;
				} else if (omit.sortThis.numSort == 1) {
					var numRight = omit.numDisplayLeft
							+ this.eleInsert.offsetWidth
							- this._staticResize.numPadding
							- this._staticResize.numLayoutLeftWidth;
					omit.numLayoutLeftMin = numLeft + omit.sortThis.numWidthMin;
					omit.numLayoutLeftMax = numRight - omit.sortThat.numWidthMin;
				}
			} else if (this.vars.varsStatus.flagSwitchNow == 'Wide'
						|| this.vars.varsStatus.flagSwitchNow == 'Classic'
			) {
				if (omit.sortThis.numSort == 0) {
					var ele = $(this.idSelf + omit.sortThis.id).down('.codeLibTemplateShadowBoxMiddleMiddle',0);
					var numLeft = omit.numDisplayLeft + ele.offsetLeft;
					var numRight = omit.numDisplayLeft
							+ this.eleInsert.offsetWidth
							- this._staticResize.numPadding
							- this._staticResize.numLayoutLeftWidth;
					omit.numLayoutLeftMin = numLeft + omit.sortThis.numWidthMin;
					omit.numLayoutLeftMax = numRight - omit.sortThat.numWidthMin;
				} else if (omit.sortThis.numSort == 1) {
					var numTop = omit.numDisplayTop + this._staticResize.numLayoutTopHeight;
					var bottom = omit.numDisplayTop
							+ this.eleInsert.offsetHeight
							- this._staticResize.numPadding
							- this._staticResize.numLayoutBottomHeight;
					omit.numLayoutTopMin = numTop + omit.sortThis.numHeightMin;
					omit.numLayoutTopMax = bottom - omit.sortThat.numHeightMin;
				}
			}
		}
		this._templateResizeNavi({evt : evt});
		evt.stop();
	},

	/**
	 *
	*/
	_templateResizeNavi : function(obj)
	{
		var ele = $(document.createElement('span'));
		var viewSize = document.viewport.getDimensions();
		var scroll = document.viewport.getScrollOffsets();
		var ZIndex = this.insRoot.setZIndex();
		/*lock*/
		var eleLock = $(document.createElement('div'));
		eleLock.addClassName('codeLibLockView');
		eleLock.setStyle({
			zIndex : ZIndex
		});
		$(this.insRoot.vars.varsSystem.id.root).insert(eleLock);
		this._varsResize.eleLock = eleLock;
		if (this._varsResize.flagArrection == 'x') {
			ele.setStyle({
				height : viewSize.height + scroll.top + 'px',
				top    : '0px',
				left   : obj.evt.pointerX() + 'px',
				zIndex : ZIndex
			});
			ele.addClassName('codeLibLayoutNaviX');
			this._varsResize.eleBarX = ele;

		} else if (this._varsResize.flagArrection == 'y') {
			ele.setStyle({
				width  : viewSize.width + scroll.left + 'px',
				top    : obj.evt.pointerY() + 'px',
				left   : '0px',
				zIndex : ZIndex
			});
			ele.addClassName('codeLibLayoutNaviY');
			this._varsResize.eleBarY = ele;

		}
		$(this.insRoot.vars.varsSystem.id.root).insert(ele);
	},

	/**
	 *
	*/
	_getResizeLeftMin : function(evt)
	{
		var data = (this._varsResize.numLayoutLeftMin > evt.pointerX()) ? 1  : 0 ;

		return data;
	},

	/**
	 *
	*/
	_getResizeLeftMax : function(evt)
	{
		var data = (this._varsResize.numLayoutLeftMax < evt.pointerX()) ? 1  : 0 ;

		return data;

	},

	/**
	 *
	*/
	_getResizeTopMin : function(evt)
	{
		var data = (this._varsResize.numLayoutTopMin > evt.pointerY()) ? 1  : 0 ;

		return data;

	},

	/**
	 *
	*/
	_getResizeTopMax : function(evt)
	{
		var data = (this._varsResize.numLayoutTopMax < evt.pointerY()) ? 1  : 0 ;

		return data;

	},

	/**
	 *
	*/
	_mousemoveResize : function(evt)
	{
		if (!this._varsResize.flag) return;
		if (this._varsResize.flagArrection == 'x') {
			if (this._getResizeLeftMin(evt)) {
				this._varsResize.eleBarX.setStyle({
					left : this._varsResize.numLayoutLeftMin + 'px',
					top  : '0px'
				});
				this._varsResize.numNaviBarXLeft = this._varsResize.numLayoutLeftMin;
				this._varsResize.numNaviBarXTop = 0;
			} else if (this._getResizeLeftMax(evt)) {
				this._varsResize.eleBarX.setStyle({
					left : this._varsResize.numLayoutLeftMax + 'px',
					top  : '0px'
				});
				this._varsResize.numNaviBarXLeft = this._varsResize.numLayoutLeftMax;
				this._varsResize.numNaviBarXTop = 0;
			} else {
				this._varsResize.eleBarX.setStyle({
					left : evt.pointerX() + 'px',
					top  : '0px'
				});
				this._varsResize.numNaviBarXLeft = evt.pointerX();
				this._varsResize.numNaviBarXTop = 0;
			}
		} else if (this._varsResize.flagArrection == 'y') {
			if (this._getResizeTopMin(evt)) {
				this._varsResize.eleBarY.setStyle({
					top  : this._varsResize.numLayoutTopMin + 'px',
					left : '0px'
				});
				this._varsResize.numNaviBarYTop = this._varsResize.numLayoutTopMin;
				this._varsResize.numNaviBarYLeft = 0;
			} else if (this._getResizeTopMax(evt)) {
				this._varsResize.eleBarY.setStyle({
					top  : this._varsResize.numLayoutTopMax + 'px',
					left : '0px'
				});
				this._varsResize.numNaviBarYTop = this._varsResize.numLayoutTopMax;
				this._varsResize.numNaviBarYLeft = 0;
			} else {
				this._varsResize.eleBarY.setStyle({
					top  : evt.pointerY() + 'px',
					left : '0px'
				});
				this._varsResize.numNaviBarYTop = evt.pointerY();
				this._varsResize.numNaviBarYLeft = 0;
			}
		}
		evt.stop();
	},

	/**
	 *
	*/
	_getResizeCalc : function()
	{
		var omit = this._varsResize;
		var difX = omit.numNaviBarXLeft - omit.numMouseLeft;
		var difY = omit.numNaviBarYTop - omit.numMouseTop;
		if (this.vars.varsStatus.flagNow == 2) {
			if (this.vars.varsStatus.flagSwitchNow == 'Standard') {
				omit.sortThis.numWidth += difX;
				omit.sortThat.numWidth -= difX;
				this._checkResizeCalcWidthMin();
			} else if (this.vars.varsStatus.flagSwitchNow == 'Wide') {
				omit.sortThis.numHeight += difY;
				omit.sortThat.numHeight -= difY;
				this._checkResizeCalcHeightMin();
			}
		} else if (this.vars.varsStatus.flagNow == 3) {
			if (this.vars.varsStatus.flagSwitchNow == 'Standard') {
				omit.sortThis.numWidth += difX;
				omit.sortThat.numWidth -= difX;
				this._checkResizeCalcWidthMin();
			} else if (this.vars.varsStatus.flagSwitchNow == 'Wide'
				|| this.vars.varsStatus.flagSwitchNow == 'Classic'
			) {
				if (omit.sortThis.numSort == 0) {
					omit.sortThis.numWidth += difX;
					omit.sortThat.numWidth -= difX;
					this._checkResizeCalcWidthMin();
				} else if (omit.sortThis.numSort == 1) {
					omit.sortThis.numHeight += difY;
					omit.sortThat.numHeight -= difY;
					this._checkResizeCalcHeightMin();
				}
			}
		}

		omit.sortThat.numWidth = (omit.sortThat.numWidthMin > omit.sortThat.numWidth)?
								  omit.sortThat.numWidthMin
								  : omit.sortThat.numWidth;
		omit.sortThis.numHeight = (omit.sortThis.numHeightMin > omit.sortThis.numHeight)?
								  omit.sortThis.numHeightMin
								  : omit.sortThis.numHeight;
		omit.sortThat.numHeight = (omit.sortThat.numHeightMin > omit.sortThat.numHeight)?
								omit.sortThat.numHeightMin
								  : omit.sortThat.numHeight;
	},

	/**
	 *
	*/
	_checkResizeCalcWidthMin : function()
	{
		var omit = this._varsResize;
		var idle = 0;
		if (omit.sortThis.numWidthMin > omit.sortThis.numWidth) {
			idle = omit.sortThis.numWidthMin - omit.sortThis.numWidth;
			omit.sortThis.numWidth += idle;
			omit.sortThat.numWidth -= idle;
		}
		if (omit.sortThat.numWidthMin > omit.sortThat.numWidth) {
			idle = omit.sortThat.numWidthMin - omit.sortThat.numWidth;
			omit.sortThis.numWidth -= idle;
			omit.sortThat.numWidth += idle;
		}
	},

	/**
	 *
	*/
	_checkResizeCalcHeightMin : function()
	{
		var omit = this._varsResize;
		var idle = 0;
		if (omit.sortThis.numHeightMin > omit.sortThis.numHeight) {
			idle = omit.sortThis.numHeightMin - omit.sortThis.numHeight;
			omit.sortThis.numHeight += idle;
			omit.sortThat.numHeight -= idle;
		}
		if (omit.sortThat.numHeightMin > omit.sortThat.numHeight) {
			idle = omit.sortThat.numHeightMin - omit.sortThat.numHeight;
			omit.sortThis.numHeight -= idle;
			omit.sortThat.numHeight += idle;
		}
	},

	/**
	 *
	*/
	_mouseupResize : function(evt)
	{
		if (!this._varsResize.flag) return;
		this.allot({
			insCurrent : this.insCurrent,
			from       : '_preMouseupResize'
		});
		this._getResizeCalc();
		this._varResizeUpdate({arr : this.vars.varsDetail});
		this.iniReload();
		this.setCake();
		this.allot({
			insCurrent : this.insCurrent,
			from       : '_mouseupResize'
		});
		if (this._varsResize.flagArrection == 'x') this._varsResize.eleBarX.remove();
		else if (this._varsResize.flagArrection == 'y') this._varsResize.eleBarY.remove();
		this._varsResize.eleLock.remove();
		evt.stop();
		this._varsResize = {};

	},

	/**
	 *
	*/
	_varResizeUpdate : function(obj)
	{
		var omit = this._varsResize;
		if (this.vars.varsStatus.flagNow == 2) {
			for (var i = 0; i < obj.arr.length; i++) {
				if ((this.idSelf + obj.arr[i].id) == (this.idSelf + omit.sortThis.id)) {
					obj.arr[i].numWidth = omit.sortThis.numWidth;
					obj.arr[i].numHeight = omit.sortThis.numHeight;
				} else if ((this.idSelf + obj.arr[i].id) == (this.idSelf + omit.sortThat.id)) {
					obj.arr[i].numWidth = omit.sortThat.numWidth;
					obj.arr[i].numHeight = omit.sortThat.numHeight;
				}
				if (!obj.arr[i].flagBoxUse) continue;
				obj.arr[i].numWidthBox =  obj.arr[i].numWidth - this._staticResize.numBoxLeftWidth * 2;
			}
		} else if (this.vars.varsStatus.flagNow == 3) {
			if (this.vars.varsStatus.flagSwitchNow == 'Standard') {
				for (var i = 0; i < obj.arr.length; i++) {
					if ((this.idSelf + obj.arr[i].id) == (this.idSelf + omit.sortThis.id)) {
						obj.arr[i].numWidth = omit.sortThis.numWidth;
						obj.arr[i].numHeight = omit.sortThis.numHeight;
					} else if ((this.idSelf + obj.arr[i].id) == (this.idSelf + omit.sortThat.id)) {
						obj.arr[i].numWidth = omit.sortThat.numWidth;
						obj.arr[i].numHeight = omit.sortThat.numHeight;
					}
					if (!obj.arr[i].flagBoxUse) continue;
					obj.arr[i].numWidthBox =  obj.arr[i].numWidth - this._staticResize.numBoxLeftWidth * 2;
				}
			} else if (this.vars.varsStatus.flagSwitchNow == 'Wide') {
				for (var i = 0; i < obj.arr.length; i++) {
					if (omit.sortThis.numSort == 0) {
						if ((this.idSelf + obj.arr[i].id) == (this.idSelf + omit.sortThis.id)) {
							obj.arr[i].numWidth = omit.sortThis.numWidth;
							obj.arr[i].numHeight = omit.sortThis.numHeight;
						} else if ((this.idSelf + obj.arr[i].id) == (this.idSelf + omit.sortThat.id)) {
							obj.arr[i].numWidth = omit.sortThat.numWidth;
							obj.arr[i].numHeight = omit.sortThat.numHeight;
						}
					} else if (omit.sortThis.numSort == 1) {
						if ((this.idSelf + obj.arr[i].id) == (this.idSelf + omit.sortThis.id)) {
							obj.arr[i].numWidth = omit.sortThis.numWidth;
							obj.arr[i].numHeight = omit.sortThis.numHeight;
						} else if ((this.idSelf + obj.arr[i].id) == (this.idSelf + omit.sortThat.id)) {
							obj.arr[i].numWidth = omit.sortThat.numWidth;
							obj.arr[i].numHeight = omit.sortThat.numHeight;
						} else {
							obj.arr[i].numHeight = omit.sortThis.numHeight;
						}
					}
					if (!obj.arr[i].flagBoxUse) continue;
					obj.arr[i].numWidthBox =  obj.arr[i].numWidth - this._staticResize.numBoxLeftWidth * 2;
				}
			} else if (this.vars.varsStatus.flagSwitchNow == 'Classic') {
				for (var i = 0; i < obj.arr.length; i++) {
					if (omit.sortThis.numSort == 0) {
						if ((this.idSelf + obj.arr[i].id) == (this.idSelf + omit.sortThis.id)) {
							obj.arr[i].numWidth = omit.sortThis.numWidth;
							obj.arr[i].numHeight = omit.sortThis.numHeight;
						} else if ((this.idSelf + obj.arr[i].id) == (this.idSelf + omit.sortThat.id)) {
							obj.arr[i].numWidth = omit.sortThat.numWidth;
							obj.arr[i].numHeight = omit.sortThat.numHeight;
						} else {
							obj.arr[i].numWidth = omit.sortThat.numWidth;
						}
					} else if (omit.sortThis.numSort == 1) {
						if ((this.idSelf + obj.arr[i].id) == (this.idSelf + omit.sortThis.id)) {
							obj.arr[i].numWidth = omit.sortThis.numWidth;
							obj.arr[i].numHeight = omit.sortThis.numHeight;
						} else if ((this.idSelf + obj.arr[i].id) == (this.idSelf + omit.sortThat.id)) {
							obj.arr[i].numWidth = omit.sortThat.numWidth;
							obj.arr[i].numHeight = omit.sortThat.numHeight;
						}

					}
					if (!obj.arr[i].flagBoxUse) continue;
					obj.arr[i].numWidthBox =  obj.arr[i].numWidth - this._staticResize.numBoxLeftWidth * 2;
				}
			}
		}
	},




	/**
	 * Move
	*/
	_iniMove : function()
	{
		if (!this.vars.varsStatus.flagMoveUse) return;
		this._templateMoveListener({arr : this.vars.varsDetail});
		this._setMoveCursor({arr : this.vars.varsDetail});
	},

	/**
	 *
	*/
	_templateMoveListener : function(obj)
	{
		if (!this.vars.varsStatus.flagMoveUse) return;
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].flagBoxUse) continue;
			this.insListener.set({
				bindAsEvent : 1, insCurrent : this, event : 'mousedown',
				strFunc : '_mousedownMove',
				ele : $((this.idSelf + obj.arr[i].id)).down('.codeLibTemplateNormalBoxMiddleMiddle', 0),
				vars : {id : (this.idSelf + obj.arr[i].id)}
			});
			this.insListener.set({
				bindAsEvent : 0, insCurrent : this, event : 'mouseover',
				strFunc : '_mouseoverMove',
				ele : $((this.idSelf + obj.arr[i].id)).down('.codeLibTemplateNormalBoxMiddleMiddle', 0),
				vars : {id : (this.idSelf + obj.arr[i].id)}
			});
			this.insListener.set({
				bindAsEvent : 0, insCurrent : this, event : 'mouseout',
				strFunc : '_mouseoutMove',
				ele : $((this.idSelf + obj.arr[i].id)).down('.codeLibTemplateNormalBoxMiddleMiddle', 0),
				vars : {id : (this.idSelf + obj.arr[i].id)}
			});
		}
		this.insListener.set({
			bindAsEvent : 1, insCurrent : this, event : 'mousemove',
			strFunc : '_mousemoveMove', ele : document, vars : ''
		});
		this.insListener.set({bindAsEvent : 1, insCurrent : this, event : 'mouseup',
			strFunc : '_mouseupMove', ele : document, vars : ''
		});
	},

	/**
	 *
	*/
	_setMoveCursor : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].flagBoxUse) continue;
			var ele = $((this.idSelf + obj.arr[i].id)).down('.codeLibTemplateNormalBoxMiddleMiddle', 0);
			ele.addClassName('codeLibBaseCursorMove');
		}
	},

	/**
	 *
	*/
	_removeMoveCursor : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].flagBoxUse) continue;
			var ele = $((this.idSelf + obj.arr[i].id)).down('.codeLibTemplateNormalBoxMiddleMiddle', 0);
			ele.removeClassName('codeLibBaseCursorMove');
		}
	},
	/**
	 *
	*/
	_removeMoveImg : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].flagBoxUse) continue;
			var id = (this.idSelf + obj.arr[i].id) + 'MoveNavi';
			$(id).down('.codeLibTemplateNormalBoxMiddleMiddle',0).removeClassName('codeLibLayoutMoveNaviYes');
		}
	},

	/**
	 *
	*/
	_mouseoverMove : function(obj)
	{
		if (!this._varsMove.flag) return;
		this._varsMove.idOver = obj.id;

		var id = obj.id + 'MoveNavi';
		$(id).down('.codeLibTemplateNormalBoxMiddleMiddle',0).addClassName('codeLibLayoutMoveNaviYes');
	},

	/**
	 *
	*/
	_mouseoutMove : function(obj)
	{
		if (!this._varsMove.flag) return;
		this._removeMoveImg({arr : this.vars.varsDetail});

		this._varsMove.idOver = this._varsMove.idDown;
	},

	/**
	 *
	*/
	_mousemoveMove : function(evt)
	{
		if (!this._varsMove.flag) return;
		this._varsMove.eleNavi.setStyle({
			left : (evt.pointerX() + this._staticMove.numNaviLeft) + 'px',
			top  : (evt.pointerY() + this._staticMove.numNaviTop) + 'px'
		});
		evt.stop();
	},

	/**
	 *
	*/
	_mousedownMove : function(evt, obj)
	{
		this._varsMove = {};
		this._varsMove = {
			flag       : 1,
			flagChange : 0,
			eleNavi    : null,
			idOver     : '',
			idDown     : obj.id
		};
		this._removeMoveCursor({arr : this.vars.varsDetail});
		this._templateMoveNavi({
			evt : evt,
			arr : this.vars.varsDetail
		});
		evt.stop();
	},

	/**
	 *
	*/
	_varsMove : {},
	_templateMoveNavi : function(obj)
	{
		var ele = $(document.createElement('div'));
		ele.addClassName('codeLibLayoutMoveNavi');
		var ZIndex = this.insRoot.setZIndex();
		ele.setStyle({
			top    : (obj.evt.pointerY() + this._staticMove.numNaviTop) + 'px',
			left   : (obj.evt.pointerX() + this._staticMove.numNaviLeft) + 'px',
			zIndex : ZIndex
		});
		$(this.insRoot.vars.varsSystem.id.root).insert(ele);

		/*Wrap Height Width*/
		if (this.vars.varsStatus.flagNow == 2) {
			if (this.vars.varsStatus.flagSwitchNow == 'Standard') {
				ele.setStyle({
					width : (this._staticMove.numWrapLayoutWidth * 2) + 'px'
				});
			} else if (this.vars.varsStatus.flagSwitchNow == 'Wide') {
				ele.setStyle({
					width : (this._staticMove.numWrapLayoutWidth) + 'px'
				});
			}
		} else if (this.vars.varsStatus.flagNow == 3) {
			if (this.vars.varsStatus.flagSwitchNow == 'Standard') {
				ele.setStyle({
					width : (this._staticMove.numWrapLayoutWidth * 3) + 'px'
				});
			} else if (this.vars.varsStatus.flagSwitchNow == 'Wide') {
				ele.setStyle({
					width  : (this._staticMove.numWrapLayoutWidth * 2) + 'px',
					height : (this._staticMove.numWrapLayoutHeight * 2) + 'px'
				});
			} else if (this.vars.varsStatus.flagSwitchNow == 'Classic') {
				ele.setStyle({
					width  : (this._staticMove.numWrapLayoutWidth * 2) + 'px',
					height : (this._staticMove.numWrapLayoutHeight * 2) + 'px'
				});
			}
		}

		/*shadowBox Height Width*/
		var data;
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].flagBoxUse) continue;
			if (this.vars.varsStatus.flagNow == 2) {
				var insTemplate = new Code_Lib_Template();
				data = insTemplate.get({
					flagType  : 'shadowBox',
					id        : (this.idSelf + obj.arr[i].id) + 'MoveNavi',
					numWidth  : this._staticMove.numLayoutWidth,
					numHeight : this._staticMove.numLayoutHeight
				});
				ele.insert(data);
				data = insTemplate.get({
					flagType  : 'normalBox',
					id        : (this.idSelf + obj.arr[i].id) + 'MoveNaviBox',
					numWidth  : this._staticMove.numBoxWidth,
					numHeight : this._staticMove.numBoxHeight
				});
				var id = (this.idSelf + obj.arr[i].id) + 'MoveNavi';
				var eleChild = $(id).down('.codeLibTemplateShadowBoxMiddleMiddle',0);
				eleChild.insert(data);
			} else if (this.vars.varsStatus.flagNow == 3) {
				if (i == 2 && this.vars.varsStatus.flagSwitchNow == 'Wide') {
					var insTemplate = new Code_Lib_Template();
					data = insTemplate.get({
						flagType  : 'shadowBox',
						numWidth  : this._staticMove.numLayoutWidth * 2 + this._staticMove.numLayoutLeftWidth * 2,
						numHeight : this._staticMove.numLayoutHeight,
						id        : (this.idSelf + obj.arr[i].id) + 'MoveNavi'
					});
					ele.insert(data);
					data = insTemplate.get({
						flagType  : 'normalBox',
						numWidth  : this._staticMove.numBoxWidth * 2
									+ this._staticMove.numLayoutLeftWidth * 2
									+ this._staticMove.numBoxLeftWidth * 2,
						numHeight : this._staticMove.numBoxHeight,
						id        : (this.idSelf + obj.arr[i].id)+'MoveNaviBox'
					});
					var id = (this.idSelf + obj.arr[i].id) + 'MoveNavi';
					var eleChild = $(id).down('.codeLibTemplateShadowBoxMiddleMiddle',0);
					eleChild.insert(data);
				} else if (i == 0 && this.vars.varsStatus.flagSwitchNow == 'Classic') {
					var insTemplate = new Code_Lib_Template();
					data = insTemplate.get({
						flagType  : 'shadowBox',
						numWidth  : this._staticMove.numLayoutWidth,
						id        : (this.idSelf + obj.arr[i].id) + 'MoveNavi',
						numHeight : this._staticMove.numLayoutHeight * 2
									+ this._staticMove.numLayoutBottomHeight
									+ this._staticMove.numLayoutTopHeight
					});
					ele.insert(data);
					data = insTemplate.get({
						flagType  : 'normalBox',
						id        : (this.idSelf + obj.arr[i].id) + 'MoveNaviBox',
						numWidth  : this._staticMove.numBoxWidth,
						numHeight : this._staticMove.numBoxHeight * 2
									+ this._staticMove.numLayoutBottomHeight
									+ this._staticMove.numLayoutTopHeight
									+ this._staticMove.numBoxLeftWidth * 2
					});
					var id = (this.idSelf + obj.arr[i].id) + 'MoveNavi';
					var eleChild = $(id).down('.codeLibTemplateShadowBoxMiddleMiddle',0);
					eleChild.insert(data);
				} else {
					var insTemplate = new Code_Lib_Template();
					data = insTemplate.get({
						flagType  : 'shadowBox',
						id        : (this.idSelf + obj.arr[i].id) + 'MoveNavi',
						numWidth  : this._staticMove.numLayoutWidth,
						numHeight : this._staticMove.numLayoutHeight
					});
					ele.insert(data);
					data = insTemplate.get({
						flagType  : 'normalBox',
						id        : (this.idSelf + obj.arr[i].id) + 'MoveNaviBox',
						numWidth  : this._staticMove.numBoxWidth,
						numHeight : this._staticMove.numBoxHeight
					});
					var id = (this.idSelf + obj.arr[i].id) + 'MoveNavi';
					var eleChild = $(id).down('.codeLibTemplateShadowBoxMiddleMiddle',0);
					eleChild.insert(data);
				}
			}
		}

		var eleBg = $(document.createElement('div'));
		eleBg.addClassName('codeLibBaseBgCcc');
		eleBg.setStyle({
			height : 100 + '%',
			width  : 100 + '%'
		});
		var id = this._varsMove.idDown + 'MoveNavi';
		$(id).down('.codeLibTemplateNormalBoxMiddleMiddle',0).insert(eleBg);
		this._varsMove.eleNavi = ele;
	},

	/**
	 *
	*/
	_mouseupMove : function(evt, obj)
	{
		if (!this._varsMove.flag) return;
		this._varsMove.flag = 0;
		this._checkMoveChange({arr : this.vars.varsDetail});

		if (this._varsMove.flagChange) {
			this.allot({
				insCurrent : this.insCurrent,
				from       : '_preMouseupMove'
			});
			/*var*/
			this._varMoveUpdate({arr : this.vars.varsDetail});
			this.iniReload();
			this.setCake();
			this.allot({
				insCurrent : this.insCurrent,
				from       : '_mouseupMove'
			});
		}
		this._setMoveCursor({arr : this.vars.varsDetail});
		this._varsMove.eleNavi.remove();
		evt.stop();
		this._varsMove = {};
	},

	/**
	 *
	*/
	_checkMoveChange : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (this._varsMove.idOver == (this.idSelf + obj.arr[i].id)
				&& this._varsMove.idDown != this._varsMove.idOver
			) {
				this._varsMove.flagChange = 1;
			}
		}
	},

	/**
	 *
	*/
	_varMoveUpdate : function(obj)
	{
		var numSortOver = 0;
		var numSortDown = 0;

		for (var i = 0; i < obj.arr.length; i++) {

			if (this._varsMove.idOver == (this.idSelf + obj.arr[i].id)) {

				numSortOver = obj.arr[i].numSort;
			} else if (this._varsMove.idDown == (this.idSelf + obj.arr[i].id)) {

				numSortDown = obj.arr[i].numSort;
			}
		}


		for (var i = 0; i < obj.arr.length; i++) {
			if (this._varsMove.idOver == (this.idSelf + obj.arr[i].id)) {
				obj.arr[i].numSort = numSortDown;

			} else if (this._varsMove.idDown == (this.idSelf + obj.arr[i].id)) {
				obj.arr[i].numSort = numSortOver;

			}
		}
		this.vars.varsDetail = obj.arr.sortBy(function(v, i) {
			return v.numSort;
		});
	},


	/**
	 *
	*/
	_staticMove : {
		numWrapLayoutWidth : 48, numWrapLayoutHeight : 48, numLayoutWidth : 36, numLayoutHeight : 36,
		numBoxWidth : 30, numBoxHeight : 30, numBoxHeightTool : 40, numBoxLeftWidth : 3,
		numLayoutTopHeight : 8, numLayoutBottomHeight : 6, numLayoutLeftWidth : 6, numNaviLeft : 15, numNaviTop : 5
	}
});
<?php }
}
?>