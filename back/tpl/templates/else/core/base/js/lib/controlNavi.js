{literal}
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_ControlNavi = Class.create(Code_Lib_ExtControl,
{

	/**
	 *
	*/
	initialize : function(obj)
	{
		this._extAllot(obj);
		this._iniVars(obj);
		this._iniWrap();
		this._iniUnder({vars : this.vars[this.vars.varsStatus.flagNow].varsFormat});
		this.insEscape = new Code_Lib_Escape();
		var str = '_ini' + this.insEscape.strCapitalize({data : this.vars.varsStatus.flagNow});

		if (this.vars.varsStatus.flagNow.match(/^folder/)) {
			this._iniFolder();
		}
		else this[str]();

		if(this.insTool) {
			this._varsTool = this.vars[this.vars.varsStatus.flagNow].varsEdit;
			this._iniTool();
		}
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
	updateVars : function()
	{
		var str = this.vars.varsStatus.flagNow;
		this.insEscape = new Code_Lib_Escape();
		var strCap = 'ins' + this.insEscape.strCapitalize({data : this.vars.varsStatus.flagNow});
		this.vars[str].varsDetail = this[strCap].vars;
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
		if(!this.vars.varsStatus.flagCakeUse) return;
		var str = 'flagNow';
		this.vars.varsStatus.flagNow = obj.data[str];
		obj.arr = this.vars.varsStatus.switchList;

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

		var flag = 0;
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i] == this.vars.varsStatus.flagNow) {
				flag = 1;
				break;
			}
		}
		if (!flag) this.vars.varsStatus.flagNow = obj.arr[0];

	},

	/**
	 *
	*/
	_setCakeVars : function(obj)
	{
		if(!this.vars.varsStatus.flagCakeUse) return;
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
	},

	/**
	 *
	*/
	_iniUnder : function(obj)
	{
		this._extUnder(obj);
	},


	/**
	 * Tool
	*/
	_iniTool : function()
	{
		this._extTool();
	},

	/**
	 * Wrap
	*/
	_iniWrap : function()
	{
		this._extWrap();
	},


	/**
	 *
	*/
	_iniTree : function()
	{
		if(!this.vars.varsStatus.flagTreeUse) return;
		this.insTree = null;
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
			insCurrent        : this.insSelf,
			idSelf            : this.idSelf + 'Tree',
			allot             : this._getTreeAllot(),
			vars              : this.vars.tree.varsDetail
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
			if(obj.from == '_mousedownBtn') {
				insCurrent.allot({
					insCurrent : insCurrent.insCurrent,
					from       : 'tree-_mousedownBtn',
					vars       : obj.vars
				});

			} else {
				obj.insCurrent = insCurrent.insCurrent;
				obj.from = 'tree-' + obj.from;
				insCurrent.allot(obj);
			}
		};

		return allot;
	},


	/**
	 *
	*/
	updateTreeVars : function(obj)
	{
		if($(this.idSelf + 'Tree')) {
			this.vars.tree.varsDetail.varsDetail = obj.vars;
			this.insTree.vars.varsDetail = obj.vars;
			this.insTree.iniReload();
		}
	},

	/**
	 *
	*/
	updateTreePageVars : function(obj)
	{
		if($(this.idSelf + 'Tree')) {
			if (this.insTree.vars.varsPage) {
				this.insTree.vars.varsPage.varsStatus.numRows = obj.vars.numRows;
				this.insTree.vars.varsPage.varsStatus.numLotNow = obj.vars.numLotNow;
			}
			this.vars.tree.varsDetail.varsDetail = obj.vars.varsDetail;
			this.insTree.vars.varsDetail = obj.vars.varsDetail;
			this.insTree.removeBtnSelect();
			this.insTree.iniReload();
		}
	},


	/**
	 * {
	 * 	vars : {},
	 * }
	*/
	updateTreeDetailLineVars : function(obj)
	{
		this._updateTreeDetailLineVars({
			vars : obj.vars,
			arr  : this.insTree.vars.varsDetail
		});
		if($(this.idSelf + 'Tree')) this.insTree.iniReload();
	},

	/**
	 *
	*/
	_updateTreeDetailLineVars : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].vars.idTarget == obj.vars.vars.idTarget) {
				obj.arr[i] = obj.vars;
				return;
			}
		}
	},


	/**
	 *
	*/
	updateTreeVarsDetail : function(obj)
	{
		if($(this.idSelf + 'Tree')) {
			this.insTree.updateBlockVars({vars : obj.vars});
			this.insTree.iniReload();
		}
	},

	/**
	 * Search
	*/
	_iniSearch : function()
	{
		if(!this.vars.varsStatus.flagSearchUse) return;
		this.insSearch = null;
		this._setSearch();
	},

	/**
	 *
	*/
	insSearch : null,
	_setSearch : function()
	{
		this.insSearch = new Code_Lib_Search({
			eleInsertBtnLeft  : this.insUnder.eleFormat.fooder.down('.codeLibBaseMarginLeftFive', 0),
			eleInsertBtnRight : this.insUnder.eleFormat.fooder.down('.codeLibBaseMarginRightFive', 0),
			eleInsert         : this.insUnder.eleFormat.body,
			insRoot           : this.insRoot,
			insCurrent        : this.insSelf,
			idSelf            : this.idSelf + 'Search',
			allot             : this._getSearchAllot(),
			vars              : this.vars.search.varsDetail
		});
	},

	/**
	 *
	*/
	_getSearchAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;

			obj.insCurrent = insCurrent.insCurrent;
			obj.from = 'search-' + obj.from;

			insCurrent.allot(obj);

		};

		return allot;
	},

	/**
	 *
	*/
	updateSearchVarsSave : function(obj)
	{
		if(this.insSearch) {
			if(this.vars.varsStatus.flagNow == 'search') {
				this.insSearch.vars.varsMyRecord.varsFormList.varsDetail = obj.vars;
				this.insSearch.rebuildMyRecord();

			} else {
				this.vars.search.varsDetail.varsMyRecord.varsFormList.varsDetail = obj.vars;
			}
		}
	},

	/**
	 *
	*/
	updateSearchVars : function(obj)
	{
		if(this.insSearch) {
			if(this.vars.varsStatus.flagNow == 'search') {
				this.insSearch.iniReloadVars({vars : obj.vars});

			} else {
				this.vars.search.varsDetail = obj.vars;
			}
		}
	},

	/**
	{
		flagNow : str ,
		varsData : [] or str,
	}
	 */
	iniAutoSearch : function(obj)
	{
		this.insSearch.iniAutoSearch(obj);
	},


	/**
	 * Folder
	*/
	_iniFolder : function()
	{
		if(!this.vars.varsStatus.flagFolderUse) return;
		this._varsFolder();
		this._setFolder();
	},

	/**
	 *
	*/
	_varsFolder : function()
	{

	},

	/**
	 *
	*/
	insFolder : null,
	_setFolder : function()
	{
		this.insEscape = new Code_Lib_Escape();
		var str = this.insEscape.strCapitalize({data : this.vars.varsStatus.flagNow});
		this['ins' + str] = new Code_Lib_Folder({
			eleInsertBtnLeft  : this.insUnder.eleFormat.fooder.down('.codeLibBaseMarginLeftFive', 0),
			eleInsertBtnRight : this.insUnder.eleFormat.fooder.down('.codeLibBaseMarginRightFive', 0),
			eleInsert         : this.insUnder.eleFormat.body,
			insRoot           : this.insRoot,
			insCurrent        : this,
			idSelf            : this.idSelf + str,
			allot             : this._getFolderAllot(),
			vars              : this.vars[this.vars.varsStatus.flagNow].varsDetail
		});
	},

	/**
	 *
	*/
	_getFolderAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;

			var data = insCurrent.vars.varsStatus.flagNow + '-' + obj.from;
			obj.insCurrent = insCurrent.insCurrent;
			obj.from = data;
			return insCurrent.allot(obj);
		};

		return allot;
	},

	/**
	 *
	*/
	updateFolderVars : function(obj)
	{

		this.insEscape = new Code_Lib_Escape();
		obj.arr = this.vars.varsStatus.switchList;
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].match(/^folder/)) {
				var strIns = 'ins' + this.insEscape.strCapitalize({data : obj.arr[i]});
				if(this[strIns]) {
					var str = this.insEscape.strCapitalize({data : this.vars.varsStatus.flagNow});
					this['ins' + str].vars.varsDetail = obj.vars;
					this['ins' + str].insTree.vars.varsDetail = obj.vars;
					this.vars[this.vars.varsStatus.flagNow].varsDetail = this['ins' + str].vars;
					this['ins' + str].iniReset();

				}
			}
		}

	},

	/**
	 *
	*/
	getVarsTreePast : function()
	{
		this.insEscape = new Code_Lib_Escape();
		if(this.vars.varsStatus.flagNow.match(/^folder/)) {
			var str = this.insEscape.strCapitalize({data : this.vars.varsStatus.flagNow});
			if (this['ins' + str]) {
				if ($(this.idSelf + str)) {
					return this['ins' + str].getVarsTreePast();
				}
			}
		}
	},

	/**
	 *
	*/
	setVarsTreePast : function(obj)
	{
		this.insEscape = new Code_Lib_Escape();
		if(this.vars.varsStatus.flagNow.match(/^folder/)) {
			var str = this.insEscape.strCapitalize({data : this.vars.varsStatus.flagNow});
			if (this['ins' + str]) {
				if ($(this.idSelf + str)) {
					return this['ins' + str].setVarsTreePast(obj);
				}
			}
		}
	},


	/**
	 *
	*/
	eventBtnSave : function()
	{
		this.insEscape = new Code_Lib_Escape();
		if(this.vars.varsStatus.flagNow.match(/^folder/)) {
			var str = this.insEscape.strCapitalize({data : this.vars.varsStatus.flagNow});
			if (this['ins' + str]) {
				if ($(this.idSelf + str)) {
					this['ins' + str].eventBtnSave();
				}
			}
		}
	},

	/**
	 *
	*/
	addVars : function(obj)
	{
		this.insEscape = new Code_Lib_Escape();
		if(this.vars.varsStatus.flagNow.match(/^folder/)) {
			var str = this.insEscape.strCapitalize({data : this.vars.varsStatus.flagNow});
			if (this['ins' + str]) {
				if ($(this.idSelf + str)) {
					this['ins' + str].addVars({vars : obj.vars});
				}
			}
		}
	},

	/**
	 *
	*/
	addLog : function(obj)
	{
		this.insEscape = new Code_Lib_Escape();
		if(this.vars.varsStatus.flagNow.match(/^folder/)) {
			var str = this.insEscape.strCapitalize({data : this.vars.varsStatus.flagNow});
			if (this['ins' + str]) {
				if ($(this.idSelf + str)) {
					this['ins' + str].addLog({vars : obj.vars});
				}
			}
		}
	},

	/**
	 *
	*/
	eventMove : function(obj)
	{
		this.insEscape = new Code_Lib_Escape();
		if(this.vars.varsStatus.flagNow.match(/^folder/)) {
			var str = this.insEscape.strCapitalize({data : this.vars.varsStatus.flagNow});
			if (this['ins' + str]) {
				if ($(this.idSelf + str)) {
					this['ins' + str].eventMove({
						vars : obj.vars
					});
				}
			}
		}
	},

	/**
	 *
	*/
	showBtn : function()
	{
		this.insEscape = new Code_Lib_Escape();
		if(this.vars.varsStatus.flagNow == 'search') {
			this.insSearch.showBtnBottom();

		} else if (this.vars.varsStatus.flagNow.match(/^folder/)) {
			var str = this.insEscape.strCapitalize({data : this.vars.varsStatus.flagNow});
			this['ins' + str].cancelLock();
			this['ins' + str].showBtnBottom();

		}
	},

	/**
	 *
	*/
	hideBtn : function()
	{
		var str = 'ins' + this.vars.varsStatus.flagNow.capitalize();
		if (this[str]) {
			return this[str].hideBtnBottom();
		}
	},

	/**
	 *
	*/
	eventRemove : function()
	{
		this.insEscape = new Code_Lib_Escape();
		var obj = {};
		obj.arr = this.vars.varsStatus.switchList;
		for (var i = 0; i < obj.arr.length; i++) {
			var str = this.insEscape.strCapitalize({data : obj.arr[i]});
			if (obj.arr[i].match(/^folder/)) {
				if ($(this.idSelf + str)) this['ins' + str].stopListener();

			} else if (obj.arr[i] == 'tree') {
				if ($(this.idSelf + str)) {
					this['ins' + str].stopListener();
				}

			} else if (obj.arr[i] == 'search') {
				if ($(this.idSelf + str)) this['ins' + str].eventRemove();
			}
		}
	},

	/**
	 *
	*/
	eventLayout : function()
	{
		this.insEscape = new Code_Lib_Escape();
		var obj = {};
		obj.arr = this.vars.varsStatus.switchList;
		for (var i = 0; i < obj.arr.length; i++) {
			var str = this.insEscape.strCapitalize({data : obj.arr[i]});
			if (obj.arr[i].match(/^folder/)) {
				if ($(this.idSelf + str)) this['ins' + str].iniReload();

			} else if (obj.arr[i] == 'tree') {
				if ($(this.idSelf + str)) this['ins' + str].iniReload();

			} else if (obj.arr[i] == 'search') {
				if ($(this.idSelf + str)) this['ins' + str].eventLayout();
			}
		}
	},


	/**
	 *
	*/
	preEventLayout : function(obj)
	{
		this.insEscape = new Code_Lib_Escape();
		obj.arr = this.vars.varsStatus.switchList;
		for (var i = 0; i < obj.arr.length; i++) {
			var str = this.insEscape.strCapitalize({data : obj.arr[i]});
			if (obj.arr[i].match(/^folder/)) {
				if ($(this.idSelf + str)) {
					if (this['ins' + str].insTree) {
						if (obj.flag == 'reset') this['ins' + str].insTree.resetScroll();
						else this['ins' + str].insTree.getScroll();
					}
				}

			} else if (obj.arr[i] == 'tree') {
				if ($(this.idSelf + str)) {
					if (obj.flag == 'reset') this['ins' + str].resetScroll();
					else this['ins' + str].getScroll();
				}

			} else if (obj.arr[i] == 'search') {
				if ($(this.idSelf + str)) this['ins' + str].getScroll();
			}
		}
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
		this.updateVars();
		this._extSwitch(obj);
	}


});
{/literal}
