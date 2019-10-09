<?php /* Smarty version 3.1.24, created on 2016-08-18 12:50:21
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/controlList.js" */ ?>
<?php
/*%%SmartyHeaderCode:115803536457b5af0dc06683_07375791%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '5a972c05428bc2789a3211463a0506fb7d7325d0' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/controlList.js',
      1 => 1471523676,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '115803536457b5af0dc06683_07375791',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_57b5af0dc69f09_51667341',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_57b5af0dc69f09_51667341')) {
function content_57b5af0dc69f09_51667341 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '115803536457b5af0dc06683_07375791';
?>

/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_ControlList = Class.create(Code_Lib_ExtControl,
{

	/**
	 *
	*/
	initialize : function(obj)
	{
		this._extAllot(obj);
		this._iniVars(obj);
		this._iniWrap();
	},

	/**
	 *
	*/
	iniReload : function()
	{
		this._extReload();
	},


	/**
	 *
	*/
	_varsSelect : [],
	_iniVars : function(obj)
	{
		this._extVars(obj);
		this.iniCake();
		this._varsSelect = [];
	},

	/**
	 *
	*/
	updateVars : function(obj)
	{
		this.vars = (Object.toJSON(obj.vars)).evalJSON();
	},


	/**
	 *
	*/
	updateVarsDetail : function(obj)
	{
		this.vars.varsDetail = obj.vars.vars.varsDetail;
		this.vars.varsHtml = obj.vars.vars.varsHtml;
		this._varsSelect = obj.vars.varsSelect;
		this.setCake();
	},

	/**
	 *
	*/
	_setVar : function()
	{
		this.vars[this.vars.varsStatus.flagNow].varsDetail.varsDetail = this.vars.varsDetail;
		this.vars[this.vars.varsStatus.flagNow].varsDetail.varsPage = this.vars.varsPage;
		this.vars[this.vars.varsStatus.flagNow].varsDetail.varsBtn = this.vars.varsBtn;
		this.vars[this.vars.varsStatus.flagNow].varsDetail.varsHtml = this.vars.varsHtml;
	},

	/**
	 * Wrap
	*/
	_iniWrap : function()
	{
		this._extWrap();
	},

	/**
	 * Cake
	*/
	iniCake : function()
	{
		this._getCake();
	},

	/**
	 *
	*/
	_getCakeVars : function(obj)
	{
		var insCurrent = obj.insReturn;
		if (obj.data) {
			insCurrent._getCakeVarsUpdate({
				arr  : insCurrent.vars.varsDetail,
				data : obj.data
			});
			insCurrent._varsCake = obj.data;
		}
	},

	/**
	 *
	*/
	_varsCake : {},
	setCake : function()
	{
		if (!this.insRoot.insCake) return;
		this._setCakeVars();
		this.insRoot.insCake.setStorageCake({
			parentKey  : this.idSelf,
			value      : this._varsCake,
			numExpires : 0
		});
	},

	/**
	 *
	*/
	_getCakeVarsUpdate : function(obj)
	{
		if (!this.vars.varsStatus.flagCakeUse) return;
		var str = 'flagNow';
		this.vars.varsStatus.flagNow = obj.data[str];

		if (this.vars.varsStatus.flagOutputUse) {
			str = 'flagOutputNow';
			if (obj.data[str] != undefined) {
				this.vars.varsStatus.flagOutputNow = obj.data[str];
			}
		}

		if (this.vars.varsStatus.flagPrintUse) {
			str = 'flagPrintNow';
			if (obj.data[str] != undefined) {
				this.vars.varsStatus.flagPrintNow = obj.data[str];
			}
		}

		if (this.vars.varsStatus.flagImportUse) {
			str = 'flagImportNow';
			if (obj.data[str] != undefined) {
				this.vars.varsStatus.flagImportNow = obj.data[str];
			}
		}

		if (this.vars.varsStatus.flagReloadUse) {
			str = 'flagReloadNow';
			if (obj.data[str] != undefined) {
				this.vars.varsStatus.flagReloadNow = obj.data[str];
			}
		}


		for (var i = 0; i < obj.arr.length; i++) {
			if (this.vars.varsStatus.flagBoldUse) {
				str = 'bold' + obj.arr[i].id;
				if (obj.data[str] == 0) {
					obj.arr[i].flagBoldNow = obj.data[str];
					obj.arr[i].strClassLoad = '';
				}
			}
			if (this.vars.varsStatus.flagFontUse) {
				str = 'font' + obj.arr[i].id;
				if (obj.data[str]) obj.arr[i].strClassFont = obj.data[str];
			}
			if (this.vars.varsStatus.flagBgUse) {
				str = 'bg' + obj.arr[i].id;
				if (obj.data[str]) obj.arr[i].strClassBg = obj.data[str];
			}
		}
	},

	/**
	 *
	*/
	_setCakeVars : function()
	{
		if (!this.vars.varsStatus.flagCakeUse) return;
		var obj = {};
		obj.arr = this.vars.varsDetail;
		var str = 'flagNow';
		this._varsCake[str] = this.vars.varsStatus.flagNow;

		if (this.vars.varsStatus.flagOutputUse) {
			str = 'flagOutputNow';
			this._varsCake[str] = this.vars.varsStatus.flagOutputNow;
		}
		if (this.vars.varsStatus.flagPrintUse) {
			str = 'flagPrintNow';
			this._varsCake[str] = this.vars.varsStatus.flagPrintNow;
		}
		if (this.vars.varsStatus.flagImportUse) {
			str = 'flagImportNow';
			this._varsCake[str] = this.vars.varsStatus.flagImportNow;
		}
		if (this.vars.varsStatus.flagReloadUse) {
			str = 'flagReloadNow';
			this._varsCake[str] = this.vars.varsStatus.flagReloadNow;
		}

		for (var i = 0; i < obj.arr.length; i++) {
			if (this.vars.varsStatus.flagBoldUse) {
				str = 'bold' + obj.arr[i].id;
				if (!obj.arr[i].flagBoldNow) this._varsCake[str] = 0;
			}
			if (this.vars.varsStatus.flagFontUse) {
				str = 'font' + obj.arr[i].id;
				if (obj.arr[i].strClassFont) this._varsCake[str] = obj.arr[i].strClassFont;
			}
			if (this.vars.varsStatus.flagBgUse) {
				str = 'bg' + obj.arr[i].id;
				if (obj.arr[i].strClassBg) this._varsCake[str] = obj.arr[i].strClassBg;
			}
		}
	},

	/**
	 *
	*/
	_iniUnder : function(obj)
	{
		this._extUnder(obj);
	},

	/**
	 *
	*/
	_updateUnder : function(obj)
	{
		if (this.vars.varsStatus.flagTreeUse) {
			if (obj.strTitleHeaderLeft != null) {
				this.vars.tree.varsFormat.strTitleHeaderLeft = obj.strTitleHeaderLeft;
			}
			if (obj.strClassHeaderLeft != null) {
				this.vars.tree.varsFormat.strClassHeaderLeft = obj.strClassHeaderLeft;
			}
		}
		if (this.vars.varsStatus.flagThumbnailUse) {
			if (obj.strTitleHeaderLeft != null) {
				this.vars.thumbnail.varsFormat.strTitleHeaderLeft = obj.strTitleHeaderLeft;
			}
			if (obj.strClassHeaderLeft != null) {
				this.vars.thumbnail.varsFormat.strClassHeaderLeft = obj.strClassHeaderLeft;
			}
		}
		if (this.vars.varsStatus.flagScheduleUse) {
			if (obj.strTitleHeaderLeft != null) {
				this.vars.schedule.varsFormat.strTitleHeaderLeft = obj.strTitleHeaderLeft;
			}
			if (obj.strClassHeaderLeft != null) {
				this.vars.schedule.varsFormat.strClassHeaderLeft = obj.strClassHeaderLeft;
			}
		}
		if (this.vars.varsStatus.flagTableUse) {
			if (obj.strTitleHeaderLeft != null) {
				this.vars.table.varsFormat.strTitleHeaderLeft = obj.strTitleHeaderLeft;
			}
			if (obj.strClassHeaderLeft != null) {
				this.vars.table.varsFormat.strClassHeaderLeft = obj.strClassHeaderLeft;
			}
		}
		if (this.vars.varsStatus.flagTableTreeUse) {
			if (obj.strTitleHeaderLeft != null) {
				this.vars.tableTree.varsFormat.strTitleHeaderLeft = obj.strTitleHeaderLeft;
			}
			if (obj.strClassHeaderLeft != null) {
				this.vars.tableTree.varsFormat.strClassHeaderLeft = obj.strClassHeaderLeft;
			}
		}
	},

	/**
	 * Tool
	*/
	_iniTool : function()
	{
		this._extTool();
	},

	/**
	 * Table
	*/
	_iniTable : function()
	{
		if (!this.vars.varsStatus.flagTableUse) return;
		this._setVar();
		this._setTable();
	},

	/**
	 *
	*/
	insTable : null,
	_setTable : function()
	{
		this.insTable = new Code_Lib_Table();
		this.insTable.iniLoad({
			eleInsertBtnLeft  : this.insUnder.eleFormat.fooder.down('.codeLibBaseMarginLeftFive',0),
			eleInsertBtnRight : this.insUnder.eleFormat.fooder.down('.codeLibBaseMarginRightFive',0),
			eleInsert         : this.insUnder.eleFormat.body,
			insRoot           : this.insRoot,
			insCurrent        : this.insSelf,
			idSelf            : this.idSelf + 'Table',
			allot             : this._getTableAllot(),
			vars              : this.vars[this.vars.varsStatus.flagNow].varsDetail
		});
	},

	/**
	 *
	*/
	_getTableAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;
			if (obj.from == '_iniVars') {
				insCurrent.insTable.setVarsBtn({
					vars : insCurrent._varsSelect
				});
			}
			else if (obj.from == 'updateVars') insCurrent.updateVarsDetail({vars : obj.vars});
			else {
				obj.insCurrent = insCurrent.insCurrent;
				obj.from = 'table-' + obj.from;
				insCurrent.allot(obj);
			}
		};

		return allot;
	},

	/**
	 *
	*/
	eventTableBtn : function(obj)
	{
		this.insTable.allot({
			insCurrent : this.insSelf,
			from       : 'eventTableBtn',
			vars       : obj.vars
		});
	},

	/**
	 *
	*/
	getTableCheckBoxArrId : function()
	{
		return this.insTable.getCheckboxLineId();
	},

	getTableCheckBoxArrIdTitle : function()
	{
		return this.insTable.getCheckboxLineIdTitle();
	},

	/**
	 * TableTree
	*/
	_iniTableTree : function()
	{
		if (!this.vars.varsStatus.flagTableTreeUse) return;
		this._setVar();
		this._setTableTree();
	},

	/**
	 *
	*/
	insTableTree : null,
	_setTableTree : function()
	{
		this.insTableTree = new Code_Lib_TableTree();
		this.insTableTree.iniLoad({
			eleInsertBtnLeft  : this.insUnder.eleFormat.fooder.down('.codeLibBaseMarginLeftFive',0),
			eleInsertBtnRight : this.insUnder.eleFormat.fooder.down('.codeLibBaseMarginRightFive',0),
			eleInsert         : this.insUnder.eleFormat.body,
			insRoot           : this.insRoot,
			insCurrent        : this.insSelf,
			idSelf            : this.idSelf + 'TableTree',
			allot             : this._getTableTreeAllot(),
			vars              : this.vars[this.vars.varsStatus.flagNow].varsDetail
		});
	},

	/**
	 *
	*/
	_getTableTreeAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;
			obj.insCurrent = insCurrent.insCurrent;
			obj.from = 'tableTree-' + obj.from;
			insCurrent.allot(obj);
		};

		return allot;
	},

	/**
	 *
	*/
	eventTableTreeBtn : function(obj)
	{
		this.insTableTree.allot({
			insCurrent : this.insSelf,
			from       : 'eventTableTreeBtn',
			vars       : obj.vars
		});
	},

	/**
	 *
	*/
	getTableTreeCheckBoxArrId : function()
	{
		return this.insTableTree.getCheckboxLineId();
	},

	/**
	 *
	*/
	updateTableTreeVars : function(obj)
	{
		if($(this.idSelf + 'TableTree')) {
			this.vars.tableTree.varsDetail.varsDetail = obj.vars.varsDetail;
			this.vars.tableTree.varsDetail.varsColumn = obj.vars.varsColumn;
			this.vars.tableTree.varsDetail.varsHtml = obj.vars.varsHtml;
			this.insTableTree.vars.varsDetail = obj.vars.varsDetail;
			this.insTableTree.vars.varsColumn = obj.vars.varsColumn;
			this.insTableTree.vars.varsHtml = obj.vars.varsHtml;
			this.insTableTree.resetBtnVars();
			this.insTableTree.iniReload();
		}
	},

	/**
	 *
	*/
	getTableTreeVarsDetail : function()
	{
		if($(this.idSelf + 'TableTree')) {
			return this.insTableTree.vars.varsDetail;
		}

		return [];
	},

	/**
	 * Schedule
	*/
	_iniSchedule : function()
	{
		if (!this.vars.varsStatus.flagScheduleUse) return;
		this._setVar();
		this._setSchedule();
	},

	/**
	 *
	*/
	insSchedule : null,
	_setSchedule : function()
	{
		this.insSchedule = new Code_Lib_Schedule();
		this.insSchedule.iniLoad({
			eleInsertBtnLeft  : this.insUnder.eleFormat.fooder.down('.codeLibBaseMarginLeftFive',0),
			eleInsertBtnRight : this.insUnder.eleFormat.fooder.down('.codeLibBaseMarginRightFive',0),
			eleInsert         : this.insUnder.eleFormat.body,
			insRoot           : this.insRoot,
			insCurrent        : this.insSelf,
			idSelf            : this.idSelf + 'Schedule',
			allot             : this._getScheduleAllot(),
			vars              : this.vars[this.vars.varsStatus.flagNow].varsDetail
		});
	},

	/**
	 *
	*/
	_getScheduleAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;
			if (obj.from == '_iniVars') insCurrent.insSchedule.varsLogBtn = insCurrent._varsSelect;
			else if (obj.from == 'updateVars') insCurrent.updateVarsDetail({vars : obj.vars});
			else {
				obj.insCurrent = insCurrent.insCurrent;
				obj.from = 'schedule-' + obj.from;
				insCurrent.allot(obj);
			}
		};

		return allot;
	},

	/**
	 * Tree
	*/
	_iniTree : function()
	{
		if (!this.vars.varsStatus.flagTreeUse) return;
		this._setVar();
		this._setTree();
	},

	/**
	 *
	*/
	insTree : null,
	_setTree : function()
	{
		this.insTree = new Code_Lib_Tree({
			eleInsertBtnLeft  : this.insUnder.eleFormat.fooder.down('.codeLibBaseMarginLeftFive', 0),
			eleInsertBtnRight : this.insUnder.eleFormat.fooder.down('.codeLibBaseMarginRightFive', 0),
			eleInsert         : this.insUnder.eleFormat.body,
			insRoot           : this.insRoot,
			insCurrent        : this,
			idSelf            : this.idSelf + 'Tree',
			allot             : this._getTreeAllot(),
			vars              : this.vars[this.vars.varsStatus.flagNow].varsDetail
		});
	},

	/**
	 *
	*/
	_getTreeAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;
			obj.insCurrent = insCurrent.insCurrent;
			obj.from = 'tree-' + obj.from;
			insCurrent.allot(obj);
		};

		return allot;
	},

	/**
	 *
	*/
	updateTreeVars : function(obj)
	{
		if($(this.idSelf + 'Tree')) {
			this.vars.tree.varsDetail.varsDetail = obj.vars.varsDetail;
			this.insTree.vars.varsDetail = obj.vars.varsDetail;
			this.insTree.iniReload();
		}
	},

	/**
	 * Thumbnail
	*/
	_iniThumbnail : function()
	{
		if (!this.vars.varsStatus.flagThumbnailUse) return;
		this._setVar();
		this._setThumbnail();
	},

	/**
	 *
	*/
	insThumbnail : null,
	_setThumbnail : function()
	{
		this.insThumbnail = new Code_Lib_Thumbnail({
			eleInsertBtnLeft  : this.insUnder.eleFormat.fooder.down('.codeLibBaseMarginLeftFive',0),
			eleInsertBtnRight : this.insUnder.eleFormat.fooder.down('.codeLibBaseMarginRightFive',0),
			eleInsert         : this.insUnder.eleFormat.body,
			insRoot           : this.insRoot,
			insCurrent        : this.insSelf,
			idSelf            : this.idSelf + 'Thumbnail',
			allot             : this._getThumbnailAllot(),
			vars              : this.vars[this.vars.varsStatus.flagNow].varsDetail
		});
	},

	/**
	 *
	*/
	_getThumbnailAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;
			if (obj.from == '_iniVars') insCurrent.insThumbnail.varsBtn = insCurrent._varsSelect;
			else if (obj.from == 'updateVars') insCurrent.updateVarsDetail({vars : obj.vars});
			else {
				obj.insCurrent = insCurrent.insCurrent;
				obj.from = 'thumbnail-' + obj.from;
				insCurrent.allot(obj);
			}
		};

		return allot;
	},

	/**
	 *
	*/
	varsEventNavi : null,
	eventNavi : function(obj)
	{
		var insEscape = new Code_Lib_Escape();
		this.eventRemove();
		this._updateUnder({strTitleHeaderLeft : obj.strTitle, strClassHeaderLeft : obj.strClass});
		this._iniUnder({vars : this.vars[this.vars.varsStatus.flagNow].varsFormat});
		var strIni = '_ini' + insEscape.strCapitalize({data : this.vars.varsStatus.flagNow});

		this[strIni]();
		if (this.insTool) {
			if (obj.varsEdit) {
				this._varsTool = obj.varsEdit;

			} else {
				this._varsTool = this.vars[this.vars.varsStatus.flagNow].varsEdit;
			}
			this._iniTool();
		}
	},


	/**
	 *
	*/
	showBtnBottom : function()
	{
		var insEscape = new Code_Lib_Escape();
		var str = insEscape.strCapitalize({data : this.vars.varsStatus.flagNow});
		if(this['ins' + str]) {
			this['ins' + str].showBtnBottom();
		}
	},

	/**
	 *
	*/
	eventRemove : function()
	{
		if (this.insTable) this.insTable.stopListener();
		if (this.insSchedule) this.insSchedule.stopListener();
		if (this.insThumbnail) this.insThumbnail.stopListener();
		if (this.insTree) this.insTree.stopListener();
		if (this.insTableTree) this.insTableTree.stopListener();
	},

	/**
	 * {
	 * 	vars : {},
	 * }
	*/
	updateVarsDetailLine : function(obj)
	{
		this.resetVars({
			vars : {
				numRows    : obj.vars.numRows,
				varsDetail : obj.vars.varsList,
				varsHtml   : obj.vars.varsHtml
			},
			flagSelect : 1,
			numLotNow  : obj.vars.numLotNow
		});
	},

	/**
	 * {
	 * 	vars : {
	 * 		numRows : int,
	 * 		varsDetail : [],
	 * 		varsHtml : '',
	 * 	},
	 * 	numLotNow : int,
	 * }
	*/
	resetVars : function(obj)
	{
		var insEscape = new Code_Lib_Escape();
		this.vars.varsPage.varsStatus.numRows = obj.vars.numRows;
		this.vars.varsPage.varsStatus.numLotNow = obj.numLotNow;
		this.setCake();
		this.vars.varsDetail = obj.vars.varsDetail;
		this.vars.varsHtml = obj.vars.varsHtml;

		this.allot({
			insCurrent : this.insCurrent,
			from       : 'resetVars',
			vars       : {
				varsDetail : this.vars.varsDetail,
				varsEdit   : (this.vars.varsEdit)? this.vars.varsEdit : {},
				varsBtn    : this.vars.varsBtn
			}
		});
		this.iniCake();
		var str = 'ins' + insEscape.strCapitalize({data : this.vars.varsStatus.flagNow});
		this[str].removeBtnSelect();
		if (!obj.flagSelect) {
			this._varsSelect = [];
		}

		this.resetVarsChild({arr : this.vars.varsStatus.switchList});

	},

	/**
	 *
	*/
	resetVarsChild : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			var str = obj.arr[i];
			this.vars[str].varsDetail.varsDetail = this.vars.varsDetail;
			this.vars[str].varsDetail.varsPage = this.vars.varsPage;
			this.vars[str].varsDetail.varsBtn = this.vars.varsBtn;
			if (this.vars[str].varsDetail.varsHtml) {
				this.vars[str].varsDetail.varsHtml = this.vars.varsHtml;
			}
			if (this.vars.varsEdit) {
				this.vars[str].varsEdit = this.vars.varsEdit;
			}
		}
	},

	/**
	 *
	*/
	eventLayout : function()
	{
		if ($(this.idSelf + 'Table')) this.insTable.iniReload();
		if ($(this.idSelf + 'Thumbnail')) this.insThumbnail.iniReload();
		if ($(this.idSelf + 'Tree')) this.insTree.iniReload();
		if ($(this.idSelf + 'Schedule')) this.insSchedule.iniReload();
		if ($(this.idSelf + 'TableTree')) this.insTableTree.iniReload();
	},

	/**
	 *
	*/
	preEventLayout : function(obj)
	{
		var str = '';
		if (obj.flag == 'reset') str = 'resetScroll';
		else str = 'getScroll';

		if ($(this.idSelf + 'Table')) this.insTable[str]();
		if ($(this.idSelf + 'Thumbnail')) this.insThumbnail[str]();
		if ($(this.idSelf + 'Tree')) this.insTree[str]();
		if ($(this.idSelf + 'Schedule')) this.insSchedule[str]();
		if ($(this.idSelf + 'TableTree')) this.insTableTree[str]();
	},

	/**
	 *
	*/
	eventTool : function(obj)
	{
		this._extEventTool(obj);
	},

	/**
	 * Switch
	*/
	_iniSwitch : function(obj)
	{
		this._extSwitch(obj);
	}
});

<?php }
}
?>