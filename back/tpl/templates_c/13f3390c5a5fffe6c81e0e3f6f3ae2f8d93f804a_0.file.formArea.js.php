<?php /* Smarty version 3.1.24, created on 2016-08-18 12:50:22
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/formArea.js" */ ?>
<?php
/*%%SmartyHeaderCode:132095060257b5af0e041608_25647194%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '13f3390c5a5fffe6c81e0e3f6f3ae2f8d93f804a' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/formArea.js',
      1 => 1471523676,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '132095060257b5af0e041608_25647194',
  'variables' => 
  array (
    'varsLoad' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_57b5af0e079627_87749200',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_57b5af0e079627_87749200')) {
function content_57b5af0e079627_87749200 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '132095060257b5af0e041608_25647194';
?>

/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_FormArea = Class.create(Code_Lib_ExtLib,
{
	
		varsLoad : <?php echo $_smarty_tpl->tpl_vars['varsLoad']->value;?>
,
	
	/**
	 *
	*/
	initialize : function(obj)
	{
		this._extAllot(obj);
		this._iniListener();
		this._iniVars(obj);
		this._iniWrap();
		this._iniTree();
		this._setBtnTogleStyle();

	},

	/**
	 *
	*/
	iniReload : function()
	{
		this.stopListener();
		this.removeWrap();
		this._iniListener();
		this._iniWrap();
		this._iniTree();
		this._setBtnTogleStyle();

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
	_getCakeVars : function(obj)
	{
		var insCurrent = obj.insReturn;
		if(obj.data) {
			insCurrent._getCakeVarsUpdate({
				data : obj.data
			});
		}
	},

	/**
	 *
	*/
	_getCakeVarsUpdate : function(obj)
	{
		var idTarget = obj.data._flagHideNow;
		if (!idTarget) {
			this._flagHideNow = 1;
		}
		this._flagHideNow = parseFloat(obj.data._flagHideNow);
	},

	/**
	 *
	*/
	_setCakeVars : function(obj)
	{
		this._varsCake._flagHideNow = this._flagHideNow;
	},

	/**
	 * Listener
	*/
	stopListener : function()
	{
		if(this.insTree) this.insTree.stopListener();
	},

	/**
	 * Wrap
	*/
	_eleWrapArea : null,
	_iniWrap : function()
	{
		this._extWrap();

		this._iniBtnArea();

		var ele = $(document.createElement('span'));
		this._eleWrapArea = ele;

		this.eleWrap.insert(this._eleWrapArea);

		this._eleWrapArea.addClassName('codeLibFormAreaWrap');
		this._eleWrapArea.setStyle({
			width  : this.vars.varsStatus.numWidth + 'px',
			height : this.vars.varsStatus.numHeight + 'px'
		});
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
	_iniBtnArea : function(obj)
	{
		if (this.vars.varsStatus.flagTogleUse) {
			this._setBtnWrap();
			this._setBtnTogle();
		}
	},

	_eleBtnWrap : null,
	_setBtnWrap : function(obj)
	{
		var ele = $(document.createElement('span'));
		this._eleBtnWrap = ele;
		this.eleWrap.insert(this._eleBtnWrap);
		this._eleBtnWrap.setStyle({
			width  : this.vars.varsStatus.numWidth + 'px'
		});

		return ele;
	},

	/**
	 *
	*/
	_flagHideNow : 1,
	_setBtnTogle : function()
	{
		var insBtnOpen = new Code_Lib_Btn();
		var id = this.idSelf + 'BtnTogleOpen';
		insBtnOpen.iniBtn({
			eleInsert  : this._eleBtnWrap,
			id         : id,
			strFunc    : '_mousedownBtnTogle',
			strTitle   : this.varsLoad.strOpen,
			insCurrent : this,
			flagATag   : null,
			path       : null,
			vars       : {}
		});
		this._setListener({ins : insBtnOpen});
		var ele = $(id);
		ele.setStyle({
			marginBottom : '5px',
			marginLeft  : '5px'
		});

		var insBtnClose = new Code_Lib_Btn();
		var id = this.idSelf + 'BtnTogleClose';
		insBtnClose.iniBtn({
			eleInsert  : this._eleBtnWrap,
			id         : id,
			strFunc    : '_mousedownBtnTogle',
			strTitle   : this.varsLoad.strClose,
			insCurrent : this,
			flagATag   : null,
			path       : null,
			vars       : {}
		});
		this._setListener({ins : insBtnClose});
		var ele = $(id);
		ele.setStyle({
			marginBottom : '5px',
			marginLeft  : '5px'
		});

		var eleWrap = $(document.createElement('span'));
		eleWrap.id = this.idSelf + 'NumRows';
		this._eleBtnWrap.insert(eleWrap);

		eleWrap.setStyle({
			marginLeft  : '10px'
		});


	},

	/**
	 *
	*/
	_updateStyleNumRows : function()
	{
		var str = this.varsLoad.strAll;
		if (this.insTree) {
			str += this.insTree.vars.varsPage.varsStatus.numRows
			+ this.varsLoad.strItem;
		} else {
			str += this.vars.varsTree.varsPage.varsStatus.numRows
			+ this.varsLoad.strItem;
		}

		$(this.idSelf + 'NumRows').innerHTML = '';
		if (parseFloat(this.vars.varsTree.varsPage.varsStatus.numRows)) {
			$(this.idSelf + 'NumRows').innerHTML = str;
		}
	},

	/**
	 *
	*/
	_mousedownBtnTogle : function(obj)
	{
		if (this._flagHideNow) {
			this._flagHideNow = 0;

		} else {
			this._flagHideNow = 1;
		}
		this.setCake();
		this._setBtnTogleStyle();
	},

	/**
	 *
	*/
	_setBtnTogleStyle : function(obj)
	{
		if (!this.vars.varsStatus.flagTogleUse) {
			return;
		}

		this._updateStyleNumRows();

		if (this._flagHideNow) {
			$(this.idSelf + 'BtnTogleClose').hide();
			$(this.idSelf + 'BtnTogleOpen').show();
			$(this.idSelf + 'NumRows').show();
			this._eleWrapArea.hide();

		} else {
			$(this.idSelf + 'BtnTogleOpen').hide();
			$(this.idSelf + 'BtnTogleClose').show();
			$(this.idSelf + 'NumRows').hide();
			this._eleWrapArea.show();
		}
	},

	/**
	 * Tree
	*/
	_iniTree : function()
	{
		this._varTree();
		this._setTree();
	},

	/**
	 *
	*/
	varsTree : null,
	_varTree : function()
	{
		if(this.vars.varsTree.varsStatus.flagPageUse) {
			var numStart = this.vars.varsTree.varsPage.varsStatus.numLotNow
						 * this.insRoot.vars.varsSystem.status.numList;
			var numEnd = numStart + this.insRoot.vars.varsSystem.status.numList;
			if(numEnd > this.vars.varsDetail.length) {
				numEnd = this.vars.varsDetail.length;
			}
			if(!this.vars.varsDetail.length) {
				this.vars.varsTree.varsPage.varsStatus.numRows = 0;
				this.vars.varsTree.varsDetail = [];
				return;
			}

			this._updateTreeVars({
				arr      : this.vars.varsDetail,
				numStart : numStart,
				numEnd   : numEnd,
				idTarget : null
			});
		} else {
			this.vars.varsTree.varsDetail = this.vars.varsDetail;
		}
	},

	/**
	 *
	*/
	_updateTreeVars : function(obj)
	{
		var arrayNew = [];
		for (var i = 0; i < obj.arr.length; i++) {
			if(obj.idTarget == obj.arr[i].vars.idTarget) continue;
			arrayNew.push(obj.arr[i]);
		}

		if(this.vars.varsDetail.length != arrayNew.length) {
			obj.numEnd--;
			if(obj.numEnd == obj.numStart) {
				if(this.insTree.vars.varsPage.varsStatus.numLotNow != 0) {
					this.insTree.vars.varsPage.varsStatus.numLotNow--;
				}
				var numLotNow = this.insTree.vars.varsPage.varsStatus.numLotNow;
				this.vars.varsTree.varsPage.varsStatus.numLotNow = numLotNow;
				obj.numStart = this.insTree.vars.varsPage.varsStatus.numLotNow
							* this.insRoot.vars.varsSystem.status.numList;
				obj.numEnd = obj.numStart + this.insRoot.vars.varsSystem.status.numList;
				if(obj.numEnd > this.vars.varsDetail.length - 1) {
					obj.numEnd = this.vars.varsDetail.length - 1;
				}
			}
		}
		this.vars.varsDetail = arrayNew;
		var array = [];
		for (var i = obj.numStart; i < obj.numEnd; i++) {
			if(!arrayNew[i]) break;
			array.push(arrayNew[i]);
		}
		this.vars.varsTree.varsPage.varsStatus.numRows = arrayNew.length;
		this.vars.varsTree.varsDetail = array;

		if(this.insTree) {
			var numLotNow = this.insTree.vars.varsPage.varsStatus.numLotNow;
			this.vars.varsTree.varsPage.varsStatus.numLotNow = numLotNow;
			this.insTree.vars.varsPage.varsStatus.numRows = arrayNew.length;
			this.insTree.vars.varsDetail = array;
		}
	},

	/**
	 *
	*/
	_resetTreeVars : function(obj)
	{
		this.insTree.vars.varsPage.varsStatus.numLotNow = 0;
		this.vars.varsTree.varsPage.varsStatus.numLotNow = 0;
		this.vars.varsDetail = [];
		this.vars.varsTree.varsPage.varsStatus.numRows = 0;
		this.vars.varsTree.varsDetail = [];
		if(this.insTree) {
			this.vars.varsTree.varsPage.varsStatus.numLotNow = 0;
			this.insTree.vars.varsPage.varsStatus.numRows = 0;
			this.insTree.vars.varsDetail = [];
		}
	},

	/**
	 *
	*/
	getTreeValueToConmmaArr : function()
	{
		var str = this._getTreeValueToConmmaArr({
			arr : this.vars.varsDetail
		});

		return str;
	},

	_getTreeValueToConmmaArr : function(obj)
	{
		var array = [];
		var num = 0;
		for (var i = 0; i < obj.arr.length; i++) {
			array[num] = obj.arr[i].vars.idTarget;
			num++;
		}
		var insEscape = new Code_Lib_Escape();
		var str = insEscape.toCommnaArr({arr : array});

		return str;
	},

	/**
	 *
	*/
	insTree : null,
	_setTree : function()
	{
		this.insTree = new Code_Lib_Tree({
			eleInsertBtnLeft  : null,
			eleInsertBtnRight : null,
			eleInsert         : this._eleWrapArea,
			insRoot           : this.insRoot,
			insCurrent        : this.insSelf,
			idSelf            : this.idSelf + 'Tree',
			allot             : this._getTreeAllot(),
			vars              : this.vars.varsTree
		});
	},

	modifyVarsMove : function(obj)
	{
		this.insTree.modifyVarsMove(obj);
	},

	/**
	 *
	*/
	_getTreeAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;
			if(obj.from == '_mousedownBarAdd') {
				insCurrent.allot({
					from       : '_mousedownBarAdd',
					insCurrent : insCurrent.insCurrent,
					vars       : {
						insCurrent : insCurrent,
						flag       : 'mousedownBarAdd'
					}
				});

			} else if(obj.from == '_mousedownBarLink') {
				insCurrent.allot({
					from       : '_mousedownBarLink',
					insCurrent : insCurrent.insCurrent,
					vars       : {
						insCurrent : insCurrent,
						flag       : 'mousedownBarLink'
					}
				});

			} else if(obj.from == '_mousedownBarRemove') {
				if(insCurrent.vars.varsTree.varsStatus.flagPageUse) {
					insCurrent._resetTreeVars({});
				}

				insCurrent.allot({
					from       : '_mousedownBarRemove',
					insCurrent : insCurrent.insCurrent,
					vars       : {
						insCurrent : insCurrent
					}
				});


			} else if(obj.from == '_mousedownRemove') {
				if(insCurrent.vars.varsTree.varsStatus.flagPageUse) {
					var numStart = insCurrent.insTree.vars.varsPage.varsStatus.numLotNow
								* insCurrent.insRoot.vars.varsSystem.status.numList;
					var numEnd = numStart + insCurrent.insRoot.vars.varsSystem.status.numList;
					if(numEnd > insCurrent.vars.varsDetail.length) {
						numEnd = insCurrent.vars.varsDetail.length;
					}
					insCurrent._updateTreeVars({
						arr      : insCurrent.vars.varsDetail,
						idTarget : obj.vars.vars.idTarget,
						numStart : numStart,
						numEnd   : numEnd
					});
				}

				insCurrent.allot({
					from       : '_mousedownRemove',
					insCurrent : insCurrent.insCurrent,
					vars       : {
						insCurrent : insCurrent,
						vars       : obj.vars
					}
				});

				return 1;

			} else if(obj.from == 'eventPage') {
				insCurrent.insTree.vars.varsPage.varsStatus.numLotNow = obj.vars.numLotNow;
				var numStart = insCurrent.insTree.vars.varsPage.varsStatus.numLotNow
							* insCurrent.insRoot.vars.varsSystem.status.numList;
				var numEnd = numStart + insCurrent.insRoot.vars.varsSystem.status.numList;
				if(numEnd > insCurrent.vars.varsDetail.length) {
					numEnd = insCurrent.vars.varsDetail.length;
				}
				insCurrent._updateTreeVars({
					arr      : insCurrent.vars.varsDetail,
					idTarget : null,
					numStart : numStart,
					numEnd   : numEnd
				});
				return 1;

			} else if(obj.from == '_mousedownMove') {
				obj.insSelf = insCurrent;
				obj.insCurrent = insCurrent.insCurrent;
				obj.from = 'eventMove';
				insCurrent.allot(obj);

			} else if(obj.from == '_mouseupMove') {
				insCurrent.vars.varsDetail = obj.vars;

			} else {
				obj.insCurrent = insCurrent.insCurrent;
				obj.from = 'tree-' + obj.from;
				insCurrent.allot(obj);
			}
		};
		return allot;
	}

});

<?php }
}
?>