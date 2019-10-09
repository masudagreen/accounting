<?php /* Smarty version 3.1.24, created on 2016-08-20 07:30:13
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/formList.js" */ ?>
<?php
/*%%SmartyHeaderCode:174048177657b80705881420_02401796%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '63ef3b60f61dcd7fca356d314adfbb6d8fefc110' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/formList.js',
      1 => 1471523676,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '174048177657b80705881420_02401796',
  'variables' => 
  array (
    'varsLoad' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_57b807058bab74_71355928',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_57b807058bab74_71355928')) {
function content_57b807058bab74_71355928 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '174048177657b80705881420_02401796';
?>

/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_FormList = Class.create(Code_Lib_ExtLib,
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
		this._iniAdd();
		this._iniLine();
		this._iniForm();
	},

	/**
	 *
	*/
	iniReload : function(obj)
	{
		this.updateVarsValue();
		this.stopListener();
		this.removeWrap();
		this._iniWrap();
		this._iniAdd();
		this._iniLine();
		this._iniForm();
	},


	/**
	 *
	*/
	_staticWrap : {numBar : 17},
	_iniWrap : function()
	{
		this._extWrap();
		this.eleWrap.style.width = this._getWrapWidth() + 'px';
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
	_iniListener : function()
	{
		this._extListener();
	},





	/**
	 * Form
	*/
	_iniForm : function()
	{
		if(!this.vars.varsStatus.flagEditUse) return;
		this._setFormListener({arr : this.vars.varsDetail});
	},

	/**
	 *
	*/
	_setFormListener : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if(!obj.arr[i].flagEditUse || !obj.arr[i].flagFormUse) continue;
			var ele = $(this.idSelf + 'Line' + obj.arr[i].id).down('.codeLibFormListForm', 0);
			ele.focus();
			this.insListener.set({ flagType15 : 1, bindAsEvent : 1, insCurrent : this, event : 'blur',
				strFunc : '_blurForm', ele : ele, vars : {vars : obj.arr[i]}
			});
			return;
		}
	},

	/**
	 *
	*/
	_blurForm : function(obj, evt) {
		if(obj) evt.stop();
		else obj = evt;
		this._varsEdit.flag = 0;
		obj.vars.value = this._escapeFormValue({
			value : $(this.idSelf + 'Line' + obj.vars.id).down('.codeLibFormListForm', 0).value
		});
		obj.vars.flagFormUse = 0;
		var flag = this.allot({
			from       : '_blurForm',
			insCurrent : this.insCurrent
		});
		if(flag) return;
		this.iniReload();
	},

	/**
	 *
	*/
	_escapeFormValue : function(obj)
	{
		this.insEscape = new Code_Lib_Escape();
		obj.value =	this.insEscape.get({data : obj.value, flagType : 'fromTag'});

		return obj.value;
	},

	/**
	 * Edit
	*/
	_mousedownEdit : function(evt,obj) {
		if(obj) evt.stop();
		else obj = evt;
		this._updateEdit({arr : this.vars.varsDetail, vars : obj.vars});
		this.iniReload();
	},

	/**
	 *
	*/
	_varsEdit : {flag : 0},
	_updateEdit : function(obj)
	{
		this._varsEdit.flag = 1;
		for (var i = 0; i < obj.arr.length; i++) {
			if(!obj.arr[i].flagEditUse) continue;
			obj.arr[i].flagFormUse = 0;
			if(obj.arr[i].id == obj.vars.id) {
				obj.arr[i].flagFormUse = 1;

			}
		}
	},

	/**
	 * Remove
	*/
	_mousedownRemove : function(evt,obj) {
		if(obj) evt.stop();
		else obj = evt;
		this._updateRemove({vars : obj.vars});
		var flag = this.allot({
			from       : '_mousedownRemove',
			insCurrent : this.insCurrent
		});
		if(flag) return;
		this.iniReload();
	},

	/**
	 *
	*/
	_updateRemove : function(obj)
	{
		this._varsBlock = {};
		this.vars.varsDetail = this._removeBlock({
			arr      : this.vars.varsDetail,
			idTarget : obj.vars.id
		});
		this._updateSortSort({arr : this.vars.varsDetail});
	},

	/**
	 * Sort
	*/
	_setSortListener : function(obj)
	{
		this.insListener.set({ bindAsEvent : 1, insCurrent : this, event : 'mousemove',
			strFunc : '_mousemoveSort', ele : document, vars : ''
		});
		this.insListener.set({ bindAsEvent : 1, insCurrent : this, event : 'mouseup',
			strFunc : '_mouseupSort', ele : document, vars : ''
		});
	},

	/**
	 *
	*/
	_varsSort : {},
	_mousedownSort : function(evt, obj) {
		evt.stop();
		this.updateVarsValue();
		this._setSortListener();
		this._varsSort = {};
		this._varsSort = {
			flag     : 1,
			ele      : evt.element(),
			vars     : obj.vars,
			varsOver : null,
			eleNavi  : null
		};
		this._setSortNavi({
			vars : obj.vars,
			evt  : evt
		});
	},

	/**
	 *
	*/
	_mouseoverSort : function(obj)
	{
		if(!this._varsSort.flag || !this.vars.varsStatus.flagSortUse) return;
		this._varsSort.varsOver = obj.vars;
		$(this.idSelf + 'LineSort' + obj.vars.id).addClassName('codeLibFormListSortLineOver');
	},

	/**
	 *
	*/
	_mouseoutSort : function(obj)
	{
		if(!this._varsSort.flag || !this.vars.varsStatus.flagSortUse) return;
		this._varsSort.varsOver = '';
		$(this.idSelf + 'LineSort' + obj.vars.id).removeClassName('codeLibFormListSortLineOver');
	},

	/**
	 *
	*/
	_staticSort : {numNaviLeft : 15, numNaviTop : 5},
	_setSortNavi : function(obj)
	{
		var zIndex = this.insRoot.setZIndex();
		this._mouseoutBtn({ vars : obj.vars });
		var eleWrap = $(document.createElement('span'));
		$(this.insRoot.vars.varsSystem.id.temp).insert(eleWrap);
		var eleLine = this._setLineTemplate({
			eleWrap      : eleWrap,
			vars         : obj.vars,
			flagFirst    : 0,
			flagListener : 0,
		});

		this._varsSort.eleNavi = eleWrap;
		eleWrap.addClassName('codeLibFormListNavi');
		eleWrap.setStyle({
			left   : (obj.evt.pointerX() + this._staticSort.numNaviLeft) + 'px',
			top    : (obj.evt.pointerY() + this._staticSort.numNaviTop) + 'px',
			zIndex : zIndex
		});
	},

	/**
	 *
	*/
	_mousemoveSort : function(evt, obj) {
		if(!this._varsSort.flag) return;
		if(obj) evt.stop();
		else obj = evt;
		this._varsSort.eleNavi.setStyle({
			top  : (evt.pointerY() + this._staticSort.numNaviTop) + 'px',
			left : (evt.pointerX() + this._staticSort.numNaviLeft) + 'px'
		});
	},

	/**
	 *
	*/
	_updateSort : function()
	{
		this._varsBlock = {};
		this._getBlock({
			arr      : this.vars.varsDetail,
			idTarget : this._varsSort.vars.id
		});
		this.vars.varsDetail = this._removeBlock({
			arr    : this.vars.varsDetail,
			idTarget : this._varsSort.vars.id
		});
		this.vars.varsDetail = this._setBlock({
			arr    : this.vars.varsDetail,
			idTarget : this._varsSort.varsOver.id,
			block  : this._varsBlock
		});
		this._updateSortSort({
			arr : this.vars.varsDetail
		});
	},

	/**
	 *
	*/
	_updateSortSort : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			obj.arr[i].numSort = i;
		}
	},

	/**
	 *
	*/
	_mouseupSort : function(evt,obj) {
		if(obj) evt.stop();
		else obj = evt;
		if(!this._varsSort.flag) return;
		this._varsSort.eleNavi.remove();
		if(this._varsSort.varsOver) {
			if(this._varsSort.vars.id != this._varsSort.varsOver.id) {
				this._updateSort();
				var flag = this.allot({
					from       : '_mouseupSort',
					insCurrent : this.insCurrent
				});
				this._varsSort = {};
				if(flag) return;
			}
		}
		this._varsSort = {};
		this.iniReload();
	},

	/**
	 * Copy
	*/
	_mousedownCopy : function(evt,obj) {
		if(obj) evt.stop();
		else obj = evt;
		this._updateCopy({vars : obj.vars});
		this.allot({
			from       : '_mousedownCopy',
			insCurrent : this.insCurrent
		});
		this.iniReload();
	},

	/**
	 *
	*/
	_updateCopy : function(obj)
	{
		this.updateVarsValue();
		this._updateCopyChild({
			arr  : this.vars.varsDetail,
			vars : obj.vars
		});
	},

	/**
	 *
	*/
	_updateCopyChild : function(obj)
	{
		var data;
		for (var i = 0; i < obj.arr.length; i++) {
			if(obj.vars.id == obj.arr[i].id) {
				data = (Object.toJSON(obj.arr[i])).evalJSON();
				data.id = new Date().getTime();
				data.numSort = obj.arr.length;
				break;
			}
		}
		this.vars.varsDetail.unshift(data);
		this._updateSortSort({arr : this.vars.varsDetail});
	},

	/**
	 * Add
	*/
	_iniAdd : function()
	{
		if(!this.vars.varsStatus.flagAddUse) return;
		this._setAddWrap();
		this._setAdd();
	},

	/**
	 *
	*/
	eleWrapAdd : null,
	_setAddWrap : function()
	{
		var ele = $(document.createElement('div'));
		ele.addClassName('codeLibFormListAddWrap');
		this.eleWrap.insert(ele);
		this.eleWrapAdd = ele;
	},

	/**
	 *
	*/
	_setAdd : function()
	{
		var str = '';
		if (this.vars.varsStatus.strAddNow) {
			if (this.varsLoad.varsWhole.str[this.vars.varsStatus.strAddNow]) {
				str = this.varsLoad.varsWhole.str[this.vars.varsStatus.strAddNow];
			} else {
				str = this.vars.varsStatus.strAddNow;
			}

		} else str = this.varsLoad.varsWhole.str.add;

		var insBtn = new Code_Lib_Btn();
		insBtn.iniBtn({
			eleInsert  : this.eleWrapAdd,
			id         : this.idSelf + 'BtnAdd',
			strFunc    : '_mousedownAdd',
			strTitle   : str,
			insCurrent : this.insSelf
		});
		this._setListener({ins : insBtn});
	},

	/**
	 *
	*/
	_mousedownAdd : function()
	{
		var flag = this.allot({
			from       : '_mousedownAdd',
			insCurrent : this
		});
		if (!flag) {
			this._updateAdd();
			this.iniReload();
		}
	},

	/**
	 *
	*/
	_updateAdd : function(obj)
	{
		var data = (Object.toJSON(this.vars.templateDetail)).evalJSON();
		data.id = new Date().getTime();
		data.numSort = this.vars.varsDetail.length;
		data.value = '';
		this.vars.varsDetail.unshift(data);
		this._updateSortSort({arr : this.vars.varsDetail});
	},

	/**
	 * Btn
	*/
	_mousedownBtn : function(evt, obj)
	{
		if(obj) evt.stop();
		else obj = evt;
		if(this._varsEdit.flag) return;
		this.allot({
			insCurrent : this.insCurrent,
			from       : '_mousedownBtn',
			vars       : obj.vars
		});
	},

	/**
	 *
	*/
	_mouseoverBtn : function(obj)
	{
		if(this._varsEdit.flag) return;
		$(this.idSelf + 'Line' + obj.vars.id).addClassName('codeLibFormListLineBtnOver');
	},

	/**
	 *
	*/
	_mouseoutBtn : function(obj)
	{
		if(this._varsEdit.flag) return;
		$(this.idSelf + 'Line' + obj.vars.id).removeClassName('codeLibFormListLineBtnOver');
	},

	/**
	 * Title
	*/
	_mouseoverTitle : function(obj)
	{
		var id = this.idSelf + 'Line' + obj.vars.id;
		$(id).down('.codeLibFormListLineTitle', 0).addClassName('codeLibFormListLineTitleOver');
	},

	/**
	 *
	*/
	_mouseoutTitle : function(obj)
	{
		var id = this.idSelf + 'Line' + obj.vars.id;
		$(id).down('.codeLibFormListLineTitle',obj.num).removeClassName('codeLibFormListLineTitleOver');
	},

	/**
	 * Line
	*/
	_iniLine : function()
	{
		this._setLineWrap();
		if(this.vars.varsStatus.flagSortUse) {
			this.vars.varsDetail = this.vars.varsDetail.sortBy(function(v, i) {
				return v.numSort;
			});
		}
		this._setLine({arr : this.vars.varsDetail});
	},

	/**
	 *
	*/
	eleWrapLine : null,
	_setLineWrap : function()
	{
		var ele = $(document.createElement('div'));
		ele.addClassName('codeLibFormListLineWrap');
		this.eleWrap.insert(ele);
		this.eleWrapLine = ele;
	},

	/**
	 * {
	 * 	eleWrap      : element,
	 * 	vars         : obj,
	 * 	flagFirst    : int,
	 * 	flagListener : int,
	 * }
	*/
	_setLineTemplate : function(obj)
	{
		var eleLine = $(document.createElement('span'));
		eleLine.unselectable = 'on';
		eleLine.addClassName('codeLibFormListLine');
		eleLine.id = this.idSelf + 'Line' + obj.vars.id;
		eleLine.setStyle({width : this._getWrapWidth() + 'px'});
		if(this.vars.varsStatus.flagBtnUse && obj.vars.flagBtnUse) {
			if (obj.flagListener) {
				this.insListener.set({ bindAsEvent : 1, insCurrent : this, event : 'mousedown',
					strFunc : '_mousedownBtn', ele : eleLine, vars : {vars : obj.vars}
				});
				this.insListener.set({ bindAsEvent : 0, insCurrent : this, event : 'mouseover',
					strFunc : '_mouseoverBtn', ele : eleLine, vars : {vars : obj.vars}
				});
				this.insListener.set({ bindAsEvent : 0, insCurrent : this, event : 'mouseout',
					strFunc : '_mouseoutBtn', ele : eleLine, vars : {vars : obj.vars}
				});
			}
		}
		if(obj.flagFirst) {
			eleLine.setStyle({
				marginTop : (this._staticLine.numMargin) + 'px'
			});
		}
		obj.eleWrap.insert(eleLine);

		if(this.vars.varsStatus.flagSortUse && obj.vars.flagSortUse) {
			var ele = $(document.createElement('span'));
			ele.addClassName('codeLibFormListSort');
			ele.addClassName('codeLibFormListBlock');
			ele.addClassName('codeLibBaseCursorMove');
			eleLine.insert(ele);
			if (obj.flagListener) {
				this.insListener.set({ bindAsEvent : 1, insCurrent : this, event : 'mousedown',
					strFunc : '_mousedownSort', ele : ele, vars : {vars : obj.vars}
				});
				this.insListener.set({ bindAsEvent : 0, insCurrent : this, event : 'mouseover',
					strFunc : '_mouseoverSort', ele : ele, vars : {vars : obj.vars}
				});
				this.insListener.set({ bindAsEvent : 0, insCurrent : this, event : 'mouseout',
					strFunc : '_mouseoutSort', ele : ele, vars : {vars : obj.vars}
				});
			}
		}
		if(this.vars.varsStatus.flagCopyUse && obj.vars.flagCopyUse) {
			var ele = $(document.createElement('span'));
			ele.addClassName('codeLibFormListCopy');
			ele.addClassName('codeLibFormListBlock');
			ele.addClassName('codeLibBaseCursorPointer');
			eleLine.insert(ele);
			if (obj.flagListener) {
				this.insListener.set({ bindAsEvent : 1, insCurrent : this, event : 'mousedown',
					strFunc : '_mousedownCopy', ele : ele, vars : {vars : obj.vars}
				});
			}
		}
		if(this.vars.varsStatus.flagRemoveUse && obj.vars.flagRemoveUse) {
			var ele = $(document.createElement('span'));
			ele.addClassName('codeLibFormListRemove');
			ele.addClassName('codeLibFormListBlock');
			ele.addClassName('codeLibBaseCursorPointer');
			eleLine.insert(ele);
			if (obj.flagListener) {
				this.insListener.set({ bindAsEvent : 1, insCurrent : this, event : 'mousedown',
					strFunc : '_mousedownRemove', ele : ele, vars : {vars : obj.vars}
				});
			}
		}
		if(this.vars.varsStatus.flagRemoveUse && obj.vars.flagEditUse) {
			var ele = $(document.createElement('span'));
			ele.addClassName('codeLibFormListEdit');
			ele.addClassName('codeLibFormListBlock');
			ele.addClassName('codeLibBaseCursorPointer');
			eleLine.insert(ele);
			if (obj.flagListener) {
				this.insListener.set({ bindAsEvent : 1, insCurrent : this, event : 'mousedown',
					strFunc : '_mousedownEdit', ele : ele, vars : {vars : obj.vars}
				});
			}
		}
		if(this.vars.varsStatus.flagFormUse && obj.vars.flagFormUse) {
			var eleTag;
			var eleForm = $(document.createElement('form'));
			eleTag = $(document.createElement('input'));
			eleTag.addClassName('codeLibFormListTag');
			eleTag.addClassName('codeLibFormListForm');
			eleTag.addClassName('codeLibBaseMarginLeftFive');
			eleTag.type = 'text';
			eleTag.value = obj.vars.value;
			eleTag.id = this.idSelf + 'LineTag' + obj.vars.id;
			var width = this._getLineFormWidth({
				vars : obj.vars
			});
			eleTag.setStyle({
				width : width + 'px'
			});
			eleForm.insert(eleTag);
			eleLine.insert(eleForm);
		} else {
			var eleTag;
			eleTag = $(document.createElement('span'));
			eleTag.addClassName('codeLibFormListTag');
			eleTag.addClassName('codeLibBaseMarginLeftFive');
			eleTag.title = obj.vars.value;

			var eleText = $(document.createElement('span'));
			eleText.addClassName('codeLibFormListLineTitle');
			eleText.addClassName('codeLibBaseCursorPointer');
			eleText.insert(obj.vars.value);

			eleTag.insert(eleText);
			eleTag.id = this.idSelf + 'LineTag' + obj.vars.id;
			var width = this._getLineFormWidth({
				vars : obj.vars
			});
			eleTag.setStyle({
				width : width + 'px'
			});
			eleLine.insert(eleTag);
			if(this.vars.varsStatus.flagBtnUse && obj.vars.flagBtnUse) {
				if (obj.flagListener) {
					this.insListener.set({ bindAsEvent : 0, insCurrent : this, event : 'mouseover',
						strFunc : '_mouseoverTitle', ele : eleText, vars : {vars : obj.vars}
					});
					this.insListener.set({ bindAsEvent : 0, insCurrent : this, event : 'mouseout',
						strFunc : '_mouseoutTitle', ele : eleText, vars : {vars : obj.vars}
					});
				}
			}
		}

		return eleLine;
	},

	/**
	 *
	*/
	_setLine : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			var eleLine = this._setLineTemplate({
				eleWrap      : this.eleWrapLine,
				vars         : obj.arr[i],
				flagFirst    : (i == 0)? 1 : 0,
				flagListener : 1,
			});

			var eleLineSort = $(document.createElement('div'));
			eleLineSort.unselectable = 'on';
			eleLineSort.id = this.idSelf + 'LineSort' + obj.arr[i].id;
			eleLineSort.addClassName('unselect');
			eleLineSort.addClassName('codeLibFormListSortLine');
			eleLineSort.setStyle({
				width : this._getWrapWidth() + 'px'
			});

			this.eleWrapLine.insert(eleLineSort);
		}
	},

	/**
	 *
	*/
	_staticLine : {numMargin : 5, numBlock : 16},
	_getLineFormWidth : function(obj)
	{
		var width = this._getWrapWidth();
		if(obj.vars.flagSortUse) {
			width = width - this._staticLine.numMargin - this._staticLine.numBlock;
		}
		if(obj.vars.flagCopyUse) {
			width = width - this._staticLine.numMargin - this._staticLine.numBlock;
		}
		if(obj.vars.flagRemoveUse) {
			width = width - this._staticLine.numMargin - this._staticLine.numBlock;
		}
		if(obj.vars.flagEditUse) {
			width = width - this._staticLine.numMargin - this._staticLine.numBlock;
		}

		return width;
	},


	/**
	 *
	*/
	updateVarsValue : function()
	{
		this._updateVarsValueChild({arr : this.vars.varsDetail});
	},

	/**
	 *
	*/
	_updateVarsValueChild : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if(this.vars.varsStatus.flagFormUse && obj.arr[i].flagFormUse) {
				if($(this.idSelf + 'Line' + obj.arr[i].id)) {
					if($(this.idSelf + 'Line' + obj.arr[i].id).down('.codeLibFormListForm', 0)) {
						obj.arr[i].value = $(this.idSelf + 'LineTag' + obj.arr[i].id).value;
					}
				}

			}
		}
	},

	/**
	 * Value
	*/
	getValueJson : function()
	{
		this.updateVarsValue();
		var str = this._getValueJson({arr : this.vars.varsDetail});

		return str;
	},

	/**
	 *
	*/
	_getValueJson : function(obj)
	{
		var array= [];
		for (var i = 0; i < obj.arr.length; i++) {
			if(obj.arr[i].value == '') continue;
			array.push(obj.arr[i].value);
		}
		var str = Object.toJSON(array);

		return str;
	}

});
<?php }
}
?>