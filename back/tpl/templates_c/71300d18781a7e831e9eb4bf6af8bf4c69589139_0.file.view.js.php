<?php /* Smarty version 3.1.24, created on 2016-08-18 12:50:23
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/view.js" */ ?>
<?php
/*%%SmartyHeaderCode:110858330157b5af0f376f98_73327742%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '71300d18781a7e831e9eb4bf6af8bf4c69589139' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/view.js',
      1 => 1471523676,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '110858330157b5af0f376f98_73327742',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_57b5af0f3e18a6_68311222',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_57b5af0f3e18a6_68311222')) {
function content_57b5af0f3e18a6_68311222 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '110858330157b5af0f376f98_73327742';
?>

/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_View = Class.create(Code_Lib_ExtLib,
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
		this._iniFormat();
		this._iniBar();
		this._iniLine();
		this._iniPage();
		this._resetFindVars();
		this._iniFind();
		this._iniBtnBottom();
	},

	/**
	 *
	*/
	iniReload : function(obj)
	{
		this._updateFindValue();
		this.stopListener();
		this.removeWrap();
		this._iniWrap();
		this._iniFormat();
		this._iniBar();
		this._iniLine();
		this._iniPage();
		this._iniFind();
		this._iniBtnBottom();
		this.setScroll();
	},

	/**
	 *
	*/
	iniReloadFind : function()
	{
		this.stopListener();
		this.removeWrap();
		this._iniWrap();
		this._iniFormat();
		this._iniBar();
		this._iniLine();
		this._iniPage();
		this._iniFind();
		this._iniBtnBottom();
		this.setScroll();
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
	_varsFind : {flag : 0, value : ''},
	_iniFind : function()
	{
		if (!this.vars.varsStatus.flagFindUse) return;
		this._setFind();
		if (this._varsFind.value) {
			$(this.idSelf + 'Find').down('.codeLibBtnSearchInput', 0).value = this._varsFind.value;
		}
	},

	/**
	 *
	*/
	_resetFindVars : function()
	{
		this._varsFind = { flag : 0, value : '' };
	},

	/**
	 *
	*/
	_setFind : function()
	{
		var insBtn = new Code_Lib_Btn();
		insBtn.iniBtnSearch({
			eleInsert  : (this.vars.varsStatus.flagInnerFindUse)?
					  this.insFormat.eleTemplate.fooder.down('.codeLibBaseMarginLeftFive', 0)
					: this.eleInsertBtnLeft,
			strFunc    : '_mousedownFind',
			insCurrent : this,
			id         : this.idSelf + 'Find',
			numWidth   : this._getFindWidth({numWidth : this.vars.varsFind.numWidth}),
			unitWidth  : 'px',
			strTitle   : this.vars.varsFind.strTitle
		});
		this._setListener({ins : insBtn});
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
		if (obj.value.empty()) {
			this._removeFindMark({arr : this.vars.varsDetail});
			this._varsFind = { flag : 0, value : '' };
			this.iniReloadFind();
			return;
		}
		if (this._varsFind.flag) this._removeFindMark({arr : this.vars.varsDetail});
		this._varsFind.flag = 1;
		this._varsFind.value = obj.value;
		this._updateFindMark({
			arr : this.vars.varsDetail,
			str : obj.value
		});
		this.iniReloadFind();
	},

	/**
	 *
	*/
	_removeFindMark : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			obj.arr[i].strTitle = obj.arr[i].strTitle.replace(
				new RegExp('<span class=" codeLibViewFindMark" style="float : none;">(.*?)</span>','gm'),
				'$1'
			);
			obj.arr[i].value = obj.arr[i].value.replace(
				new RegExp('<span class=" codeLibViewFindMark" style="float : none;">(.*?)</span>','gm'),
				'$1'
			);
		}
	},

	/**
	 *
	*/
	_updateFindMark : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			var str = obj.str;
			obj.arr[i].strTitle = obj.arr[i].strTitle.replace(
				new RegExp('('+str+')','gm'),
				'<span class=" codeLibViewFindMark" style="float : none;">$1</span>'
			);
			obj.arr[i].value = obj.arr[i].value.replace(
				new RegExp('('+str+')','gm'),
				'<span class=" codeLibViewFindMark" style="float : none;">$1</span>'
			);
		}
	},

	/**
	 *
	*/
	_updateFindValue : function()
	{
		if (!this.vars.varsStatus.flagFindUse) return;
		if (!$(this.idSelf + 'Find').down('.codeLibBtnSearchInput',0).value.empty()) {
			this._varsFind = {
				flag  : 1,
				value : $(this.idSelf + 'Find').down('.codeLibBtnSearchInput', 0).value
			};
		}
	},

	/**
	 *
	*/
	_staticLine : {numBlock : 16, numIdle : 5, numMargin : 5, numBar : 17, strDummy : '*', numContentIdle : 20},
	strLine : {},
	_iniLine : function()
	{
		this._setLineWrap();
		this._setLine({arr : this.vars.varsDetail});
	},

	/**
	 *
	*/
	eleWrapLine : null,
	_setLineWrap : function()
	{
		var ele = $(document.createElement('div'));
		ele.addClassName('codeLibViewLineWrap');
		this.insFormat.eleTemplate.body.insert(ele);
		this.eleWrapLine = ele;
	},

	/**
	 *
	*/
	_getLineWidth : function()
	{
		var array = this.eleWrap.style.width.split('px');
		var data = parseFloat(array[0] - this._staticLine.numBar);

		return data;
	},

	/**
	 *
	*/
	_setLine : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			var eleLine = $(document.createElement('div'));
			eleLine.addClassName('codeLibViewLine');
			eleLine.id = this.idSelf + 'Line' + obj.arr[i].id;
			this.eleWrapLine.insert(eleLine);
			eleLine.setStyle({width : this._getLineWidth() + 'px'});

			var eleHeaderWrap = $(document.createElement('span'));
			eleHeaderWrap.addClassName('codeLibViewLineHeaderWrap');
			eleHeaderWrap.setStyle({
				width : this._getLineWidth() + 'px'
			});
			eleLine.insert(eleHeaderWrap);
			if (i > 0) {
				var eleHeaderSeparate = $(document.createElement('span'));
				eleHeaderSeparate.addClassName('codeLibViewLineSeparate');
				eleHeaderSeparate.setStyle({
					width : this._getLineWidth() + 'px'
				});
				eleHeaderWrap.insert(eleHeaderSeparate);
			}

			var eleHeader = $(document.createElement('span'));
			eleHeader.addClassName('codeLibViewLineHeader');
			eleHeader.setStyle({
				width : this._getLineWidth() + 'px'
			});
			eleHeaderWrap.insert(eleHeader);

			var eleFold = $(document.createElement('span'));
			eleFold.addClassName('codeLibViewLineFold');
			eleFold.addClassName('codeLibBaseMarginLeftFive');
			eleFold.addClassName('codeLibViewLineBlock');
			if (obj.arr[i].flagFoldUse && this.vars.varsStatus.flagFoldUse) {
				if (obj.arr[i].flagFoldNow) eleFold.addClassName('codeLibViewLineFoldClose');
				else eleFold.addClassName('codeLibViewLineFoldOpen');
				eleFold.addClassName('codeLibBaseCursorPointer');
				eleFold.unselectable = 'on';
				eleFold.addClassName('unselect');
			}
			eleHeader.insert(eleFold);
			if (obj.arr[i].flagFoldUse && this.vars.varsStatus.flagFoldUse) {
				this.insListener.set({
					bindAsEvent : 1, insCurrent : this, event : 'mousedown',
					strFunc : '_mousedownFold', ele : eleFold, vars : { vars : obj.arr[i] }
				});
			}

			var eleTitle = $(document.createElement('span'));
			eleTitle.addClassName('codeLibViewLineTitle');
			eleTitle.addClassName('codeLibBaseFontBold');
			eleTitle.addClassName('codeLibBaseMarginLeftFive');
			var numWidth = this._getLineWidth()
						- this._staticLine.numBlock
						- this._staticLine.numMargin
						- this._staticLine.numIdle;
			eleTitle.setStyle({
				width : numWidth + 'px'
			});
			eleTitle.title = obj.arr[i].strTitle.replace(
				new RegExp('<.*<?php echo '?>'; ?>
(.*?)</.*<?php echo '?>'; ?>
','gm'),
				'$1'
			);
			eleTitle.insert(obj.arr[i].strTitle);
			eleHeader.insert(eleTitle);

			var eleBodyWrap = $(document.createElement('div'));
			eleBodyWrap.addClassName('codeLibViewLineBodyWrap');
			eleLine.insert(eleBodyWrap);
			if (obj.arr[i].flagFoldNow) eleBodyWrap.hide();

			var eleHeaderSeparate = $(document.createElement('span'));
			eleHeaderSeparate.addClassName('codeLibViewLineSeparate');
			eleHeaderSeparate.setStyle({
				width : this._getLineWidth() + 'px'
			});
			eleBodyWrap.insert(eleHeaderSeparate);

			var eleBody = $(document.createElement('span'));
			eleBody.addClassName('codeLibViewLineBody');
			eleBody.setStyle({
				width : this._getLineWidth() + 'px'
			});
			eleBodyWrap.insert(eleBody);

			var eleBodyValue;
			if ((obj.arr[i].flagTag == 'input' && obj.arr[i].flagType == 'checkbox') || obj.arr[i].flagTag == 'select') {
				eleBodyValue = $(document.createElement('form'));
			}
			else eleBodyValue = $(document.createElement('span'));
			eleBody.addClassName('codeLibViewLineBodyValue');
			eleBodyValue.setStyle({
				width : this._getLineWidth() + 'px'
			});
			eleBody.insert(eleBodyValue);

			if (this.vars.varsStatus.flagLineStatusUse) {
				var eleWrap = $(document.createElement('div'));
				eleWrap.addClassName('codeLibViewLineBodyStatusWrap');
				eleBody.insert(eleWrap);

				var eleSeparate = $(document.createElement('span'));
				eleSeparate.addClassName('codeLibViewLineSeparateDot');
				eleSeparate.setStyle({
					width : this._getLineWidth() + 'px'
				});
				eleWrap.insert(eleSeparate);

				var eleStatus = $(document.createElement('span'));
				eleStatus.addClassName('codeLibViewLineBodyStatus');
				eleStatus.setStyle({
					width : this._getLineWidth() + 'px'
				});
				eleWrap.insert(eleStatus);

			}

			if (obj.arr[i].flagContentUse) {
				var ele = $(document.createElement('div'));
				ele.addClassName('codeLibViewLineContent');
				ele.setStyle({
					width : (this._getLineWidth() - this._staticLine.numContentIdle) + 'px'
				});
				eleBodyValue.insert(ele);

			} else {
				var eleValue;
				if (obj.arr[i].flagTag == 'input' && obj.arr[i].flagType == 'checkbox') {
					eleValue = $(document.createElement('ul'));
					eleValue.addClassName('codeLibViewLineValue');
					eleValue.id = this.idSelf + 'Value' + obj.arr[i].id;
					this._setLineCheckbox({
						arr       : obj.arr[i].arrayOption,
						now       : obj.arr[i].value,
						eleInsert : eleTag
					});
					eleBodyValue.insert(eleValue);

				} else if (obj.arr[i].flagTag == 'select') {
					eleValue = $(document.createElement(obj.arr[i].flagTag));
					eleValue.addClassName('codeLibViewLineValue');
					eleValue.id = this.idSelf + 'Value' + obj.arr[i].id;
					eleValue.disabled = 'true';
					this._setLineSelect({
						arr       : obj.arr[i].arrayOption,
						now       : obj.arr[i].value,
						eleInsert : eleTag,
						vars      : obj.arr[i]
					});
					eleBodyValue.insert(eleValue);

				} else if (obj.arr[i].flagTag == 'input' && obj.arr[i].flagType == 'password') {
					eleValue = $(document.createElement('div'));
					eleValue.addClassName('codeLibViewLineValue');
					eleValue.id = this.idSelf + 'Value' + obj.arr[i].id;
					var strDummy = '';
					for (var j = 0; j < obj.arr[i].value.length; j++) {
						strDummy += this._staticLine.strDummy;
					}
					eleValue.insert(strDummy);
					eleBodyValue.insert(eleValue);

				} else {
					eleValue = $(document.createElement('div'));
					eleBodyValue.insert(eleValue);

					eleValue.id = this.idSelf + 'Value' + obj.arr[i].id;
					if (obj.arr[i].varsTextBtn) {
						var arrTextBtn = obj.arr[i].varsTextBtn;
						for (var j = 0; j < arrTextBtn.length; j++) {
							if (arrTextBtn[j].strTitle === '') {
								continue;
							}
							var insBtn = new Code_Lib_Btn();
							var id = this.idSelf + 'Value' + obj.arr[i].id + arrTextBtn[j].id;
							insBtn.iniBtnText({
								eleInsert  : eleValue,
								id         : id,
								strFunc    : 'checkTextBtn',
								strTitle   : arrTextBtn[j].strTitle,
								insCurrent : this,
								vars       : arrTextBtn[j].vars
							});
							$(id).addClassName('codeLibViewLineValue');
							this._setListener({ins : insBtn});
						}

					} else {
						eleValue.addClassName('codeLibViewLineValue');
						eleValue.insert(obj.arr[i].value);
					}
				}

			}

			if (i == obj.arr.length - 1) {
				var eleSeparate = $(document.createElement('span'));
				eleSeparate.addClassName('codeLibViewLineSeparate');
				eleSeparate.setStyle({
					width : this._getLineWidth() + 'px'
				});
				eleLine.insert(eleSeparate);
			}
		}
	},

	/**
	 *
	*/
	checkTextBtn : function(obj)
	{
		this.allot({
			insCurrent : this.insCurrent,
			insSelf    : this,
			from       : 'checkTextBtn',
			vars       : obj.vars
		});
	},

	/**
	 *
	*/
	_setLineSelect : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			var ele = $(document.createElement('option'));
			ele.value = obj.arr[i].value;
			if (obj.now == obj.arr[i].value) ele.selected = 'true';
			ele.insert(obj.arr[i].strTitle);
			obj.eleInsert.insert(ele);
		}
	},

	/**
	 *
	*/
	_setLineCheckbox : function(obj)
	{
		var array = obj.now.split('-');
		for (var i = 0; i < obj.arr.length; i++) {
			var ele = $(document.createElement('li'));
			if (Prototype.Browser.IE) ele.addClassName('ie');
			else if (Prototype.Browser.Gecko) ele.addClassName('firefox');

			var eleTag = $(document.createElement('input'));
			eleTag.id = this.idSelf + obj.arr[i].id;
			eleTag.value = obj.arr[i].value;
			eleTag.disabled = 'true';

			for (var j = 0; j < array.length; j++) {
				if (array[j] == obj.arr[i].value) eleTag.selected = 'true';
			}
			ele.insert(obj.arr[i].strTitle);
			obj.eleInsert.insert(ele);
		}
	},

	/**
	 *
	*/
	insBar : null,
	_iniBar : function()
	{
		if (!this.vars.varsStatus.flagBarUse) return;
		this._setBarWrap();
		this._setBar();
	},

	/**
	 *
	*/
	_staticBar : {numHeight : 16, numWidth : 16},
	eleWrapBar : null,
	_setBarWrap : function()
	{
		var ele = $(document.createElement('div'));
		ele.addClassName('codeLibViewBarWrap');
		ele.unselectable = 'on';
		ele.addClassName('unselect');
		this.insFormat.eleTemplate.header.down('.codeLibBaseMarginLeftFive', 0).insert(ele);
		ele.setStyle({height : this._staticBar.numHeight + 'px'});
		this.eleWrapBar = ele;
	},

	/**
	 *
	*/
	_setBar : function(obj) {

		/*fold*/
		var eleFold = $(document.createElement('span'));
		eleFold.addClassName('codeLibViewBarFold');
		if (this.vars.varsStatus.flagFoldUse) {
			eleFold.addClassName('codeLibBaseCursorPointer');
			if (this.vars.varsStatus.flagFoldNow) eleFold.addClassName('codeLibViewLineFoldClose');
			else  eleFold.addClassName('codeLibViewLineFoldOpen');
		}
		eleFold.unselectable = 'on';
		eleFold.addClassName('unselect');

		this.eleWrapBar.insert(eleFold);
		this._setBarFoldListener({ele : eleFold});

	},

	/**
	 *
	*/
	_setBarFoldListener : function(obj)
	{
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
		this._updateBarFoldVar();
		this._updateBarFoldStyle();
		this._updateBarFoldStyleAll({arr : this.vars.varsDetail});
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
		var ele = this.eleWrapBar.down('.codeLibViewBarFold', 0);
		ele.removeClassName('codeLibViewLineFoldOpen');
		ele.removeClassName('codeLibViewLineFoldClose');
		var cut = this.vars.varsStatus.flagFoldNow;
		if (!cut) ele.addClassName('codeLibViewLineFoldOpen');
		else ele.addClassName('codeLibViewLineFoldClose');
	},

	/**
	 *
	*/
	_updateBarFoldStyleAll : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].flagFoldUse) continue;
			var ele = $(this.idSelf + 'Line' + obj.arr[i].id).down('.codeLibViewLineFold', 0);
			ele.removeClassName('codeLibViewLineFoldOpen');
			ele.removeClassName('codeLibViewLineFoldClose');
			if (obj.arr[i].flagFoldNow) {
				$(this.idSelf + 'Line' + obj.arr[i].id).down('.codeLibViewLineBodyWrap', 0).hide();
				ele.addClassName('codeLibViewLineFoldClose');
			} else {
				$(this.idSelf + 'Line' + obj.arr[i].id).down('.codeLibViewLineBodyWrap', 0).show();
				ele.addClassName('codeLibViewLineFoldOpen');
			}
		}
	},

	/**
	 *
	*/
	_mousedownFold : function(evt, obj)
	{
		if (obj) evt.stop();
		else obj = evt;
		this._updateFoldVar({arr : this.vars.varsDetail, vars : obj.vars});
		this._updateFoldStyle({arr : this.vars.varsDetail, vars : obj.vars});
	},

	/**
	 *
	*/
	_updateFoldVarAll : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].flagFoldUse) continue;
			obj.arr[i].flagFoldNow = obj.flagFoldNow;
		}
	},

	/**
	 *
	*/
	_updateFoldVar : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].flagFoldUse) continue;
			if (this.idSelf + 'Line' + obj.arr[i].id == this.idSelf + 'Line' + obj.vars.id) {
				if (obj.arr[i].flagFoldNow) obj.arr[i].flagFoldNow = 0;
				else obj.arr[i].flagFoldNow = 1;
			}
		}
	},

	/**
	 *
	*/
	_updateFoldStyle : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].flagFoldUse) continue;
			if (this.idSelf + 'Line' + obj.arr[i].id == this.idSelf + 'Line' + obj.vars.id) {
				var eleFold = $(this.idSelf + 'Line' + obj.arr[i].id).down('.codeLibViewLineFold', 0);
				eleFold.removeClassName('codeLibViewLineFoldOpen');
				eleFold.removeClassName('codeLibViewLineFoldClose');
				if (!obj.arr[i].flagFoldNow) {
					var ele = $(this.idSelf + 'Line' + obj.arr[i].id).down('.codeLibViewLineBodyWrap', 0);
					new Effect.BlindDown(ele, {
						duration : 0.5
					});
					eleFold.addClassName('codeLibViewLineFoldOpen');
				} else {
					var ele = $(this.idSelf + 'Line' + obj.arr[i].id).down('.codeLibViewLineBodyWrap', 0);
					new Effect.BlindUp(ele, {
						duration : 0.5
					});
					eleFold.addClassName('codeLibViewLineFoldClose');
				}
			}
		}
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
	_iniBtnBottom : function()
	{
		this._extBtnBottom();
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
	_getPageAllot : function()
	{
		var allot =  function(obj)
		{
			var insCurrent = obj.insCurrent;
			if (obj.from == 'checkPage') {
				insCurrent.allot({
					insCurrent : insCurrent.insCurrent,
					from       : 'checkPage',
					vars       : obj.vars
				});
			}
		};

		return allot;
	},

	/**
	 *
	*/
	_iniFormat : function()
	{
		this._extFormat();
	},

	/**
	 *
	*/
	_iniWrap : function()
	{
		this._extWrap();
		this.eleWrap.style.width = this._getWrapWidth() + 'px';
		this.eleWrap.style.height = this._getWrapHeight() + 'px';
	}

});
<?php }
}
?>