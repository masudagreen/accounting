<?php /* Smarty version 3.1.24, created on 2019-08-08 23:26:18
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/plugin/accounting/js/file.js" */ ?>
<?php
/*%%SmartyHeaderCode:16687344305d4caf9a7775c8_50212879%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '90225919b8df2908a07c00c958e401c72d002ee7' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/plugin/accounting/js/file.js',
      1 => 1560675143,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '16687344305d4caf9a7775c8_50212879',
  'variables' => 
  array (
    'varsLoad' => 0,
    'numNews' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_5d4caf9a894840_16107914',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_5d4caf9a894840_16107914')) {
function content_5d4caf9a894840_16107914 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '16687344305d4caf9a7775c8_50212879';
?>

/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Plugin_Accounting_File = Class.create(Code_Lib_ExtPortal,
{

	vars : <?php echo $_smarty_tpl->tpl_vars['varsLoad']->value;?>
,
	numNews : <?php echo $_smarty_tpl->tpl_vars['numNews']->value;?>
,


	/**
	 *
	*/
	initialize : function()
	{
		this._iniCss();
	},

	/**
	 *
	*/
	iniLoad : function(obj)
	{
		this._iniVars(obj);
		this._iniListener();
		this._iniPopup();
		this._iniLayout();
		this._iniList();
		this._iniDetail();
	},

	/**
	 * Listener
	*/
	insListener : null,
	_iniListener : function()
	{
		this.insListener = new Code_Lib_Listener();
		this._varsListener = [];
	},

	/**
	 *
	*/
	_varsListener : [],
	_setListener : function(obj)
	{
		var data = {ins : obj.ins};
		this._varsListener.push(data);
	},

	/**
	 *
	*/
	stopListener : function()
	{
		if (!this._varsListener.length) {
			return;
		}
		this.insListener.stop();
		this._stopListenerChild({arr : this._varsListener});
		this._resetListener();
	},

	/**
	 *
	*/
	_stopListenerChild : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			obj.arr[i].ins.insListener.stop();
		}
	},

	/**
	 *
	*/
	_resetListener : function()
	{
		this._varsListener = [];
	},
	/**
		{
			flag :
			vars   : [],
		}
	*/
	_flagAutoDetail : 0,
	_flagAutoSearchOver : '',
	bootAutoSearchOver : function(obj)
	{
		this._flagAutoSearchOver = obj.flag;
		if (obj.flag == 'addFile') {
			this._setDetailChild({flag : 'add',flagBack : 'LogEditor'});

		} else if (obj.flag == 'addFileCash') {
			this._setDetailChild({flag : 'add', flagBack : 'CashEditor'});

		} else if (obj.flag == 'showFile') {
			var vars = {};
			vars.vars = obj.vars;
			this._flagAutoDetail = 1;
			this.bootAutoSearch({vars : vars});

		} else if (obj.flag == 'showLog') {
			this._eventListConnect({flag : 'Reload', flagType : 'start'});
		}

	},

	checkAutoSearch : function(obj)
	{
		this._checkAutoSearch(obj);
	},

	_flagAutoData : '',
	_varsAutoData : {},
	_checkAutoSearch : function(obj)
	{
		var insEscape = new Code_Lib_Escape();
		this._flagAutoData = obj.flag;

		if (this._flagAutoData == 'Log' || this._flagAutoData == 'Cash') {
			this._varsAutoData = {
				vars     : {
					arrFile : [],
				},
				flag     : 'setFile',
				flagFrom : obj.flagFrom
			};
			if (obj.flagFrom == 'detail') {
				var temp = {};
				temp.id = this.insDetail.varsEventList.vars.vars.idTarget;
				temp.strTitle = this.insDetail.varsEventList.vars.strTitle;
				this._varsAutoData.vars.arrFile.push(temp);

			} else if (obj.flagFrom == 'list') {
				var varsTitle = this.insList.getTableCheckBoxArrIdTitle();
				var arr = this.insList.getTableCheckBoxArrId();
				for (var i = 0; i < arr.length; i++) {
					var temp = {};
					temp.id = arr[i];
					temp.strTitle = varsTitle[arr[i]];
					this._varsAutoData.vars.arrFile.push(temp);
				}
			}

			var varsData = this.insTop.checkChildData({idTarget : obj.flag});
			if (!varsData) {
				var idTarget = insEscape.strLowCapitalize({data : obj.flag});
				this.insTop.iniAutoBoot({
					idTarget       : idTarget + 'Window',
					flagHideWindow : 0,
					insBack        : this,
					strBackFunc    : 'eventAutoSearch'
				});

			} else {
				if (varsData.insWindow.vars.flagHideNow) {
					varsData.insWindow.updateHide({ flagEffect : 1 });

				} else {
					varsData.insWindow.setZIndex();
				}
				this.eventAutoSearch();
			}
			this.insList.showBtnBottom();

		} else if (this._flagAutoData == 'LogEditor') {
			this._varsAutoData = {
				vars  : obj.vars,
				flag  : 'addFile'
			};
			this.eventAutoSearch();

		} else if (this._flagAutoData == 'CashEditor') {
			this._varsAutoData = {
				vars  : obj.vars,
				flag  : 'addFile'
			};
			this.eventAutoSearch();
		}

	},

	eventAutoSearch : function()
	{
		if (this._flagAutoData == 'Log' || this._flagAutoData == 'Cash') {
			var varsData = this.insTop.checkChildData({idTarget : this._flagAutoData});
			varsData.insClass.bootAutoSearchOver(this._varsAutoData);
			if (this._varsAutoData.flagFrom == 'detail') {
				this.insDetail.showBtnBottom();
			}

		} else if (this._flagAutoData == 'LogEditor') {
			var varsData = this.insTop.checkChildData({idTarget : 'Log'});
			varsData.insClass.bootAutoSearchOver(this._varsAutoData);

		} else if (this._flagAutoData == 'CashEditor') {
			var varsData = this.insTop.checkChildData({idTarget : 'Cash'});
			varsData.insClass.bootAutoSearchOver(this._varsAutoData);
		}

	},

	/**

	*/
	bootAutoSearch : function(obj)
	{
		var insEscape = new Code_Lib_Escape();
		var flag = 'Reload';
		var flagLock = this.insLayout.checkToolLock({from : 'list', idTarget : flag});
		if (flagLock) {
			return;
		}
		this._resetSearch();
		var varsData = [];
		var varsTmpl = {flagType: '', strColumn: '', flagCondition: 'eq', value: ''};
		var str = insEscape.strLowCapitalize({data : obj.vars.vars.idTarget});
		if (str == 'arrSpaceStrTag') {
			var flagTag = this.insTop.bootWindowTag({
				strTarget : obj.vars.vars.strTag
			});
			if (flagTag) {
				return;
			}
			varsTmpl.flagType = 'tag';
			varsTmpl.flagCondition = 'like';
			varsTmpl.strColumn = str;
			varsTmpl.value = ' ' + obj.vars.vars.strTag + ' ';
			varsData.push(varsTmpl);

		} else if (str == 'id' || str == 'idLogFile') {
			varsTmpl.flagType = 'num';
			varsTmpl.strColumn = 'idLogFile';
			varsTmpl.value = obj.vars.vars[str];
			varsData.push(varsTmpl);

		} else if (str == 'strTitle') {
			varsTmpl.flagType = 'str';
			varsTmpl.strColumn = str;
			varsTmpl.value = obj.vars.vars[str];
			varsData.push(varsTmpl);

		} else if (str == 'strFileType') {
			varsTmpl.flagType = 'type';
			varsTmpl.strColumn = str;
			varsTmpl.value = obj.vars.vars[str];
			varsData.push(varsTmpl);

		} else if (str == 'numSize') {
			varsTmpl.flagType = 'num';
			varsTmpl.strColumn = 'numByte';
			varsTmpl.value = obj.vars.vars[str];
			varsData.push(varsTmpl);

		} else if (str == 'idAccount') {
			varsTmpl.flagType = 'account';
			varsTmpl.strColumn = str;
			varsTmpl.value = obj.vars.vars[str];
			varsData.push(varsTmpl);
		}

		this._varsSearch.ph.arrWhere = varsData;
		if (str == 'strStatus') {
			if (parseFloat(obj.vars.vars[str])) {
				this._varsSearch.ph.flagApply = 'remove';
			} else {
				this._varsSearch.ph.flagApply = 'done';
			}
			this._varsSearch.ph.arrWhere = [];

		} else {
			this._varsSearch.ph.flagApply = 'none';
		}
		this._eventListConnect({flag : flag});
	},


	/**
	 *
	*/
	_iniPopup : function()
	{
		this._extPopup();
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
	_iniLayout : function()
	{
		this._extLayout();
	},

	/**
	 *
	*/
	_getLayoutAllot : function()
	{
		var allot =  function(obj)
		{
			var insCurrent = obj.insCurrent;
			if (obj.from == 'eventLayout') {
				if (insCurrent.insNavi) insCurrent.insNavi.eventLayout();
				if (insCurrent.insList) insCurrent.insList.eventLayout();
				if (insCurrent.insDetail) insCurrent.insDetail.eventLayout();

			} else if (obj.from == 'preEventLayout') {
				insCurrent._preEventLayout({flag : 'dummy'});

			} else if (obj.from == 'navi-_mousedownNavi') {
				if (obj.vars.id == 'Switch') {
					insCurrent.insNavi.eventTool({idTarget : insCurrent.insNavi.vars.varsStatus.flagNow, flagLoop:1});

				} else if (obj.vars.id == 'Reload') {
					insCurrent._preEventLayout({flag : 'reset'});
					insCurrent._eventNaviConnect({vars : obj.vars, flag : insCurrent.insNavi.vars.varsStatus.flagNow + '-reload'});

				} else if (obj.vars.id == 'Preference') {
					insCurrent._iniChild({
						strTitleParent : insCurrent.insWindow.vars.strTitle,
						strTitleChild  : insCurrent.vars.child.varsTitle[obj.vars.id],
						strExt         : insCurrent.strExt,
						strChild       : obj.vars.id,
						strClass       : insCurrent.strClass,
						idModule       : insCurrent.idModule
					});
				}

			} else if (obj.from == 'navi-_mousedownMenu') {
				return insCurrent.insNavi.vars.varsStatus.flagNow;

			} else if (obj.from == 'navi-_mousedownLine') {
				insCurrent.insNavi.eventTool({idTarget : obj.vars});

			} else if (obj.from == 'list-_mousedownNavi') {
				if (obj.vars.id == 'Switch') {
					insCurrent.insList.eventTool({idTarget : insCurrent.insList.vars.varsStatus.flagNow, flagLoop:1});

				} else if (obj.vars.id == 'Preference' || obj.vars.id == 'Search') {
					insCurrent._iniChild({
						strTitleParent : insCurrent.insWindow.vars.strTitle,
						strTitleChild  : insCurrent.vars.child.varsTitle[obj.vars.id],
						strExt         : insCurrent.strExt,
						strChild       : obj.vars.id,
						strClass       : insCurrent.strClass,
						idModule       : insCurrent.idModule
					});

				} else if (obj.vars.id == 'Import') {
					insCurrent._preEventLayout({flag : 'reset'});
					insCurrent._eventListConnect({flag : obj.vars.id});

				} else if (obj.vars.id == 'Reload') {
					insCurrent._eventListConnect({flag : obj.vars.id, flagType : insCurrent.insList.vars.varsStatus.flagReloadNow});

				}

			} else if (obj.from == 'list-_mousedownMenu') {
				if (obj.vars.id == 'Switch') {
					return insCurrent.insList.vars.varsStatus.flagNow;

				} else if (obj.vars.id == 'Output' || obj.vars.id == 'Print') {
					return insCurrent.insList.vars.varsStatus.flagOutputNow;

				} else if (obj.vars.id == 'Reload') {
					return insCurrent.insList.vars.varsStatus.flagReloadNow;
				}

			} else if (obj.from == 'list-_mousedownLine') {
				if (obj.varsTarget == 'Switch') {
					return insCurrent.insList.eventTool({idTarget : obj.vars});

				} else if (obj.varsTarget) {
					insCurrent.insList.eventTool({idTarget : obj.vars, flagStr : obj.varsTarget});
					insCurrent._eventListConnect({flag : obj.varsTarget, flagType : obj.vars});
					return;
				}

			} else if (obj.from == 'detail-_mousedownLine') {
				insCurrent._eventDetailVersion({vars : obj.vars, idTarget : obj.vars});

			} else if (obj.from == 'detail-_mousedownNavi') {

				if (obj.vars.id == 'Reload') {
					insCurrent._preEventLayout({flag : 'reset'});
					insCurrent._eventDetailConnect({flag : 'reload'});

				} else if (obj.vars.id == 'Add'
						|| obj.vars.id == 'Copy'
						|| obj.vars.id == 'Edit'
				) {
					var userAgent = window.navigator.userAgent.toLowerCase();
					var appVersion = window.navigator.appVersion.toLowerCase();
					if (userAgent.indexOf("msie") > -1) {
						if (appVersion.indexOf("msie 8.0") > -1) {
							alert(insCurrent.vars.varsItem.strBrowser);
							return;
						}
					}
					insCurrent._setDetailChild({flag : obj.vars.id.toLowerCase()});

				} else if (obj.vars.id == 'Preference') {
					insCurrent._iniChild({
						strTitleParent : insCurrent.insWindow.vars.strTitle,
						strTitleChild  : insCurrent.vars.child.varsTitle[obj.vars.id],
						strExt         : obj.vars.id,
						strChild       : '',
						strClass       : insCurrent.strClass,
						idModule       : insCurrent.idModule
					});
				}
			}
		};

		return allot;
	},

	/**
	 *
	*/
	_iniList : function()
	{
		this._extList();
	},

	/**
	 *
	*/
	_extList : function()
	{
		this._updateVarsBtnHide({
			arr    : this.vars.portal.varsList.varsDetail,
			arrBtn : this.vars.portal.varsList.varsBtn,
		});
		this._setList();
		this._setListStart();
	},

	/**
	 *
	*/
	_checkListBtn : function(obj)
	{
		this._updateVarsBtnHide({
			arr    : obj.varsDetail,
			arrBtn : obj.varsBtn,
		});
	},

	/**
	 *
	*/
	_updateVarsBtnHide : function(obj)
	{
		var flagDelete = 0;
		var flagLog = 0;
		var flagCash = 0;

		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].flagBtnDelete) flagDelete = 1;
			if (obj.arr[i].flagBtnLog) flagLog = 1;
			if (obj.arr[i].flagBtnCash) flagCash = 1;
		}

		for (var i = 0; i < obj.arrBtn.length; i++) {
			obj.arrBtn[i].flagUse = 0;
			if (obj.arrBtn[i].vars.idTarget == 'Delete' && flagDelete) obj.arrBtn[i].flagUse = 1;
			else if (obj.arrBtn[i].vars.idTarget == 'Log' && flagLog) obj.arrBtn[i].flagUse = 1;
			else if (obj.arrBtn[i].vars.idTarget == 'Cash' && flagCash) obj.arrBtn[i].flagUse = 1;
		}
	},

	/**
	 *
	*/
	eventChildSearchConnect : function(obj)
	{
		this._varsSearch = obj.varsSearch;
		var temp = {};
		temp.numLotNow = this._varsSearch.numLotNow;
		this._eventListConnect({
			flag        : obj.flag,
			strBackFunc : obj.strBackFunc,
			insBack     : obj.insBack,
			vars        : temp
		});
	},

	/**
	 *
	*/
	_eventListConnect : function(obj)
	{
		if (obj.flag == 'Output' || obj.flag == 'Print') {
			this._eventSearch({
				numLotNow : this._varsSearch.numLotNow,
				ph : {
					flagApply : this._varsSearch.ph.flagApply,
					arrWhere  : this._varsSearch.ph.arrWhere,
					arrOrder  : this._varsSearch.ph.arrOrder
				}
			});

		} else if (obj.flag == 'Reload') {
			if (obj.flagType == 'start') {
				this._resetSearch();
			}
			this._eventSearch({
				numLotNow : this._varsSearch.numLotNow,
				ph : {
					flagApply : this._varsSearch.ph.flagApply,
					arrWhere  : this._varsSearch.ph.arrWhere,
					arrOrder  : this._varsSearch.ph.arrOrder
				}
			});

		} else if (obj.flag == 'Search') {
			this._eventSearch({
				numLotNow : obj.vars.numLotNow,
				ph : {
					flagApply : this._varsSearch.ph.flagApply,
					arrWhere  : this._varsSearch.ph.arrWhere,
					arrOrder  : this._varsSearch.ph.arrOrder
				}
			});

		} else if (obj.flag == 'Delete') {
			var arrId = this.insList.getTableCheckBoxArrId();
			if (!arrId.length) {
				alert(this.insRoot.vars.varsSystem.str.selectRequire);
				this.insList.eventNavi({strTitle : null, strClass : null});
				return;
			}
			this._eventValue({
				vars     : this.insList.getTableCheckBoxArrId(),
				idTarget : ''
			});

		} else if (obj.flag == 'Log') {
			var arrId = this.insList.getTableCheckBoxArrId();
			if (!arrId.length) {
				alert(this.insRoot.vars.varsSystem.str.selectRequire);
				this.insList.eventNavi({strTitle : null, strClass : null});
				return;
			}
			this._checkAutoSearch({flag : obj.flag, flagFrom : 'list'});
			return;

		} else if (obj.flag == 'Cash') {
			var arrId = this.insList.getTableCheckBoxArrId();
			if (!arrId.length) {
				alert(this.insRoot.vars.varsSystem.str.selectRequire);
				this.insList.eventNavi({strTitle : null, strClass : null});
				return;
			}
			this._checkAutoSearch({flag : obj.flag, flagFrom : 'list'});
			return;

		} else if (obj.flag == 'Import') {
			this._eventSearch({
				numLotNow : this._varsSearch.numLotNow,
				ph : {
					flagApply : this._varsSearch.ph.flagApply,
					arrWhere  : this._varsSearch.ph.arrWhere,
					arrOrder  : this._varsSearch.ph.arrOrder
				}
			});
			this.insLayout.updateTool({flagLock : 1, from : 'list', idTarget : 'Import'});
		}

		this._varsListConnect = obj;
		this._sendListConnect();
	},


	/**
	 *
	*/
	_eventListConnectSuccess : function(obj)
	{
		if (obj.json.flag == 1){
			if (this._varsListConnect.flag == 'Search'
				|| this._varsListConnect.flag == 'Reload'
				|| this._varsListConnect.flag == 'Import'
			) {
				this.insLayout.updateTool({flagLock : 0, from : 'list', idTarget : 'Reload'});
				this.insList.resetVars({vars : obj.json.data, numLotNow : this._varsSearch.numLotNow});
				this.insList.eventNavi({strTitle : null, strClass : null});
				if (this._varsListConnect.flag == 'Reload') {
					if (this._flagAutoDetail) {
						if (obj.json.data.numRows) {
							this._eventDetailList({vars : obj.json.data.varsDetail[0]});
							this._flagAutoDetail = 0;
						}
					}
				}

			} else if (this._varsListConnect.flag == 'Delete' ) {
				this.insList.resetVars({vars : obj.json.data, numLotNow : 0});
				this.insList.eventNavi({strTitle : null, strClass : null});
				this._resetDetail();

			} else if (this._varsListConnect.flag == 'Print' ) {
				this.insRoot.setPrint({data : obj.json.data, insCurrent : this, strFunc : 'eventListConnectSuccessPrint'});
			}

		} else if (obj.json.flag == 10) {
			if (this._varsListConnect.flag == 'Search'
				|| this._varsListConnect.flag == 'Reload'
			) {
				this.insLayout.updateTool({flagLock : 0, from : 'list', idTarget : 'Reload'});
				this.insList.eventNavi({strTitle : null, strClass : null});
			}

		} else if (obj.json.flag == 40) {
			this.insList.resetVars({vars : obj.json.data, numLotNow : this._varsSearch.numLotNow});
			this.insList.eventNavi({strTitle : null, strClass : null});

		} else {
			if (obj.json) {
				if (obj.json.flag) {
					if (this.insRoot.vars.varsSystem.str[obj.json.flag]) {
						alert(this.insRoot.vars.varsSystem.str[obj.json.flag]);
					}
				}
			}
		}
	},

	/**
	 *
	*/
	_iniDetail : function()
	{
		this._extDetail();
	},

	/**
	 *
	*/
	_getDetailAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;
			if (obj.from == 'eventRemove-detail') insCurrent._eventRemoveDetailContent();
			else if (obj.from == '_mousedownMove') insCurrent.insNavi.eventMove({vars : obj.vars});
			else if (obj.from == 'view-preEventLayout') insCurrent._preEventLayoutDetailContent();
			else if (obj.from == 'view-eventLayout') insCurrent._eventLayoutDetailContent();
			else if (obj.from == 'view-checkTextBtn') insCurrent._checkDetailContentTextBtn({vars : obj.vars});
			else if (obj.from == 'view-eventBtnBottom') {
				if (obj.vars.vars.vars.idTarget == 'Log' || obj.vars.vars.vars.idTarget == 'Cash') {
					insCurrent._checkAutoSearch({flag : obj.vars.vars.vars.idTarget, flagFrom : 'detail'});
				} else {
					insCurrent._eventDetailConnect({flag : obj.vars.vars.vars.idTarget});
				}
			}

		};

		return allot;
	},

	/**
	 *
	*/
	_eventDetailConnectLog : function(obj)
	{
		this.bootAutoSearch(obj);
	},



	/**
	 *
	*/
	_checkDetailContentTextBtn : function(obj)
	{
		this.bootAutoSearch(obj);
	},



	/**
	 *
	*/
	_numDetailContent : 0,
	_setDetailContent : function(obj)
	{
		this._numDetailContent = 0;

		this._iniDetailSpace();
		this._iniDetailSpaceHtml();
	},

	_varsDetailSpace : {},
	_iniDetailSpace : function(obj)
	{
		this._varsDetailSpace = {};
		this._setDetailSpace({arr : this.insDetail.insView.vars.varsDetail});
	},

	/**
	 *
	*/
	_eleImg : null,
	_setDetailSpace : function(obj)
	{
		this._eleImg = null;
		var strTitle = '';
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == 'StrTitle') {
				strTitle = obj.arr[i].value;
				break;
			}
		}
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].varsSpace) continue;
			if (!obj.arr[i].varsData.varsColumnDetail.strFileType.match(/^(png|jpeg|jpg|gif|bmp)$/i)) {
				continue;
			}
			var ele = this.insDetail.insView.eleWrap.down('.codeLibViewLineContent', this._numDetailContent);
			this._numDetailContent++;
			var insSpace = new Code_Lib_Space({
				eleScroll  : this.insDetail.insView.eleInsert,
				eleInsert  : ele,
				insRoot    : this.insRoot,
				insCurrent : this,
				idSelf     : this.idSelf + 'Space' + obj.arr[i].id,
				allot      : {},
				vars       : obj.arr[i].varsSpace
			});
			this._varsDetailSpace[obj.arr[i].id] = insSpace;

			var ele = insSpace.eleWrap;
			var eleImg = $(document.createElement('img'));
			var idImg = insSpace.idSelf + 'img';

			var numWidth = 0;
			if (parseFloat(obj.arr[i].varsData.vars.numWidth) > 0) {
				numWidth = parseFloat(obj.arr[i].varsData.vars.numWidth);
				eleImg.style.width = numWidth + 'px';
			}
			var numHeight = 0;
			if (parseFloat(obj.arr[i].varsData.vars.numHeight) > 0) {
				numHeight = parseFloat(obj.arr[i].varsData.vars.numHeight);
				eleImg.style.height = numHeight + 'px';
			}

			eleImg.id = idImg;
			var str = '?';
			str += 'class=' + this.strClass + '&';
			str += 'module=' + this.idModule + '&';
			str += 'ext=' + this.strExt + '&';
			str += 'child=' + this.strChild + '&';
			str += 'func=' + 'DetailImg' + '&';
			str += 'db=' + 'slave' + '&';
			str += 'idTarget=' + obj.arr[i].varsData.vars.idTarget + '&';
			str += 'numVersion=' + obj.arr[i].varsData.numVersion + '&';
			var pathUrl = this.insRoot.vars.varsSystem.path.file + str;
			eleImg.src = pathUrl;
			eleImg.addClassName('codeLibBaseCursorPointer');
			eleImg.title = obj.arr[i].varsData.varsColumnDetail.strTitle;
			ele.insert(eleImg);
			this._eleImg = eleImg;
			this.insListener.set({
				bindAsEvent : 1, insCurrent : this, event : 'mousedown',
				strFunc : '_mousedownBtn', ele : this._eleImg,
				vars : {vars : {
					idImg     : idImg,
					pathUrl   : pathUrl,
					numWidth  : numWidth,
					numHeight : numHeight,
					strTitle  : strTitle
				}}
			});
			break;
		}
	},

	/**
	 *
	*/
	_pathUrl : '',
	_pWin : {},
	_mousedownBtn : function(evt, obj)
	{
		evt.stop();
		var numIdle = 30;
		var numWidth = 500;
		if (obj.vars.numWidth) {
			numWidth = obj.vars.numWidth;
		}
		var numHeight = 500;
		if (obj.vars.numHeight) {
			numHeight = obj.vars.numHeight;
		}

		if (this._pathUrl == obj.vars.pathUrl) {
			if(!this._pWin.closed) {
				this._pWin.close();
				this._pWin = null;
				this._pathUrl = '';
				return;
			}
		}

		var pWin = window.open("", "dummy", "width=" + (numWidth + numIdle) + ", height=" + (numHeight + numIdle));
		pWin.document.open();
		pWin.document.write('<!DOCTYPE html>\n');
		pWin.document.write('<html lang="ja">\n');
		pWin.document.write('<head>\n');
		pWin.document.write('<meta charset="utf-8"/>\n');
		pWin.document.write('<title>' + obj.vars.strTitle + '</title>\n');
		pWin.document.write('</head>\n');
		pWin.document.write('<body>\n');
		if (obj.vars.numWidth && obj.vars.numHeight) {
			pWin.document.write('<img src="' + obj.vars.pathUrl + '" width="' + numWidth + '" height="' + numHeight + '" onclick="window.close();"/>\n');
		} else {
			pWin.document.write('<img src="' + obj.vars.pathUrl + '" onclick="window.close();"/>\n');
		}

		pWin.document.write('</body>\n');
		pWin.document.write('</html>\n');
		pWin.document.close();
		pWin.focus();
		this._pWin = pWin;
		this._pathUrl = obj.vars.pathUrl;
	},

	_updateDetailSpace : function(obj)
	{
		this._varsDetailSpace = {};
		this._setDetailSpaceLayout({arr : this.insDetail.insView.vars.varsDetail});
	},

	_setDetailSpaceLayout : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].varsSpace) continue;
			if (!obj.arr[i].varsData.varsColumnDetail.strFileType.match(/^(png|jpeg|jpg|gif|bmp)$/i)) {
				continue;
			}
			var ele = this.insDetail.insView.eleWrap.down('.codeLibViewLineContent', this._numDetailContent);
			this._numDetailContent++;
			var insSpace = new Code_Lib_Space({
				eleScroll  : this.insDetail.insView.eleInsert,
				eleInsert  : ele,
				insRoot    : this.insRoot,
				insCurrent : this,
				idSelf     : this.idSelf + 'Space' + obj.arr[i].id,
				allot      : {},
				vars       : obj.arr[i].varsSpace
			});
			this._varsDetailSpace[obj.arr[i].id] = insSpace;
			var ele = insSpace.eleWrap;
			if (this._eleImg) {
				ele.insert(this._eleImg);
			}
			break;
		}
	},

	/**
	 *
	*/
	_varsDetailSpaceHtml : {},
	_iniDetailSpaceHtml : function()
	{
		this._varsDetailSpaceHtml = {};
		this._setDetailSpaceHtml({arr : this.insDetail.insView.vars.varsDetail});
	},

	/**
	 *
	*/
	_setDetailSpaceHtml : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id != 'JsonChargeHistory') continue;
			var ele = this.insDetail.insView.eleWrap.down('.codeLibViewLineContent', this._numDetailContent);
			this._numDetailContent++;
			ele.insert(obj.arr[i].strHtml);
			num = 1;
			var arr = obj.arr[i].jsonChargeHistory;
			for (var j = 0; j < arr.length; j++) {
				var idTr = this.idSelf + obj.arr[i].id + '_Tr' + num;
				var idTd = idTr + '_Td' + 'idAccount';
				$(idTd).innerHTML = '';
				var insBtn = new Code_Lib_Btn();
				var vars = {};
				vars.idTarget = 'idAccount';
				vars.idAccount = arr[j].idAccount;
				insBtn.iniBtnTextTarget({
					eleInsert  : $(idTd),
					id         : idTd + '_' + num,
					strFunc    : '_checkDetailContentTextBtn',
					strTitle   : arr[j].strCodeName,
					insCurrent : this,
					vars       : vars
				});
				this._setListener({ins : insBtn});
				num++;
			}
			break;
		}
	},

	/**
	 *
	*/
	_eventLayoutDetailContent : function()
	{
		if (!this.insDetail.varsEventList) return;
		this._numDetailContent = 0;
		this._updateDetailSpace();
		this._iniDetailSpaceHtml();
	},

	/**
	 *
	*/
	_preEventLayoutDetailContent : function()
	{
		if (!this.insDetail.varsEventList) return;
		if (this._eleImg) {
			$(this.insRoot.vars.varsSystem.id.temp).insert(this._eleImg);
		}
	},

	/**
	 *
	*/
	_eventRemoveDetailContent : function()
	{
		if (!this.insDetail.varsEventList) return;
		if (this._varsListener.length) {
			this.stopListener();
		}
	},

	/**
	 *
	*/
	_setDetailChild : function(obj)
	{
		var objDetail = (Object.toJSON(this.vars.portal.varsDetail.templateDetail)).evalJSON();
		var varsDetail = [];
		var varsIni = null;
		var vars = {};

		if (obj.flag == 'add') {
			varsDetail = this._getDetailChildVars({
				arr  : objDetail,
				flag : obj.flag
			});

		} else if (obj.flag == 'copy') {
			varsIni = this._getDetailChildVars({
				flag    : 'add',
				arr     : (Object.toJSON(objDetail)).evalJSON(),
				vars    : this.insDetail.varsEventList.vars
			});
			varsDetail = this._getDetailChildVars({
				flag : obj.flag,
				arr  : objDetail,
				vars : this.insDetail.varsEventList.vars
			});
			obj.flag = 'add';
			vars = this.insDetail.varsEventList.vars;

		} else if (obj.flag == 'edit') {
			varsIni = this._getDetailChildVars({
				flag    : 'editIni',
				arr     : (Object.toJSON(objDetail)).evalJSON(),
				vars    : this.insDetail.varsEventList.vars
			});
			varsDetail = this._getDetailChildVars({
				flag : obj.flag,
				arr  : objDetail,
				vars : this.insDetail.varsEventList.vars
			});
			vars = this.insDetail.varsEventList.vars;
		}

		var idTarget = 0;

		if (this.insDetail.varsEventList.vars) {
			if (this.insDetail.varsEventList.vars.vars) {
				idTarget = this.insDetail.varsEventList.vars.vars.idTarget;

			}
		}

		this._extChild({
			strTitleParent  : this.insWindow.vars.strTitle,
			strTitleChild   : this.vars.child.varsTitle.editor,
			strExt          : this.strExt,
			strChild        : 'Editor',
			strClass        : this.strClass,
			idModule        : this.idModule,
			varsChild       : {
				flagType    : obj.flag,
				flagBack    : (obj.flagBack)? obj.flagBack : '',
				idTarget    : idTarget,
				varsDetail  : varsDetail,
				varsIni     : varsIni,
				vars        : vars
			}
		});

	},

	/**
	 *
	*/
	_getDetailChildVars : function(obj)
	{
		var insDisplay = new Code_Lib_TimeDisplay();
		var arrayNew = [];

		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == 'StrTitle') {
				if (obj.vars) {
					if (obj.flag == 'editIni') {
						obj.arr[i].value = '';
						arrayNew.push(obj.arr[i]);

					} else if (obj.flag == 'edit') {
						obj.arr[i].value = obj.vars.strTitle;
						arrayNew.push(obj.arr[i]);
					}
				}

			} else if (obj.arr[i].id == 'Upload') {
				var strChild = 'Editor';
				obj.arr[i].arrayHidden = [
					{id : 'class',      value : this.strClass},
					{id : 'module',     value : this.idModule},
					{id : 'ext',        value : this.strExt},
					{id : 'child',      value : strChild},
					{id : 'db',         value : 'master'},
					{id : 'idUpload',   value : this.idSelf + this.strExt + strChild},
					{id : 'idTag',      value : obj.arr[i].id},
					{id : 'cache',      value : (new Date()).getTime()},
					{id : 'jsonSearch', value : Object.toJSON(this._varsSearch)},
					{id : 'token',      value : (this.insRoot.vars.varsSystem.token)? this.insRoot.vars.varsSystem.token : ''},
				];
				obj.arr[i].value = 'dummy';
				arrayNew.push(obj.arr[i]);

				if (obj.flag == 'edit' || obj.flag == 'editIni') {
					obj.arr[i].arrayHidden.push({id : 'func',   value : 'DetailEdit'});
					this._eventValue({
						vars     : '',
						idTarget : obj.vars.vars.idTarget
					});
					obj.arr[i].arrayHidden.push({id : 'jsonValue',  value : Object.toJSON(this._varsValue)});

				} else {
					obj.arr[i].arrayHidden.push({id : 'func',   value : 'DetailAdd'});
					obj.arr[i].strExplain = obj.arr[i].varsTmpl.add;
				}
				obj.arr[i].flagHideNow = 1;
				if (obj.flag == 'add' || obj.flag == 'copy') {
					obj.arr[i].flagHideNow = 0;
				}

			} else if (obj.arr[i].id == 'IdAccountCharge') {
				if (obj.vars) {
					if (obj.flag == 'edit' || obj.flag == 'editIni') {
						obj.arr[i].flagMustUse = 1;
						var data = obj.arr[i].varsTmpl;
						var num = obj.vars.vars.jsonChargeHistory.length - 1;
						obj.arr[i].strExplain = data.replace(RegExp('<?php echo '<%'; ?>
strCodeName<?php echo '%>'; ?>
', "g"), obj.vars.vars.jsonChargeHistory[num].strCodeName);
						arrayNew.push(obj.arr[i]);
					}
				}

			} else if (obj.arr[i].id == 'ArrSpaceStrTag') {
				if (obj.flag == 'add' || obj.flag == 'editIni') {
					obj.arr[i].value = '';

				} else {
					obj.arr[i].value = (obj.vars.arrSpaceStrTag)? obj.vars.arrSpaceStrTag : '';
				}
				arrayNew.push(obj.arr[i]);
			}
		}

		return arrayNew;
	},

	/**
	 *
	*/
	_eventDetailList : function(obj)
	{
		var objData = this._updateDetailListVars({
			flagVersion  : (obj.flagVersion)? 1 : 0,
			vars         : obj.vars
		});
		this.insDetail.eventList(objData);
		this._setDetailContent({idTarget : obj.vars.vars.idTarget});
	},

	/**
	 *
	*/
	_updateDetailListVars : function(obj)
	{
		var objDetail = (Object.toJSON(this.vars.portal.varsDetail.templateDetail)).evalJSON();
		var varsBtn = (Object.toJSON(this.vars.portal.varsDetail.varsBtn)).evalJSON();
		var varsEdit = (Object.toJSON(this.vars.portal.varsDetail.view.varsEdit)).evalJSON();

		this._updateDetailTool({
			arr  : this.insDetail.insTool.vars.varsDetail,
			vars : obj.vars
		});

		var objData = {
			flagMoveUse : 1,
			strTitle    : this.insDetail.vars.varsStart.strTitle,
			strClass    : obj.vars.strClass,
			vars        : {
				varsDetail : this._updateDetailListVarsChild({
					flagVersion  : (obj.flagVersion)? 1 : 0,
					arr          : objDetail,
					vars         : obj.vars
				}),
				varsBtn    : this._updateDetailListVarsBtn({
					flagVersion  : (obj.flagVersion)? 1 : 0,
					arr          : varsBtn,
					vars         : obj.vars
				}),
				varsEdit   : this._updateDetailListVarsEdit({
					flagVersion  : (obj.flagVersion)? 1 : 0,
					arr          : varsEdit,
					vars         : obj.vars
				}),
				vars       : obj.vars
			}
		};

		return objData;
	},

	/**
	 *
	*/
	_updateDetailListVarsEdit : function(obj)
	{
		obj.arr.flagAddUse = 0;
		obj.arr.flagCopyUse = 0;
		obj.arr.flagEditUse = 0;

		if (obj.vars.flagBtnAdd) {
			obj.arr.flagAddUse = 1;
			obj.arr.flagCopyUse = 1;
		}

		if (obj.vars.flagBtnEdit) {
			obj.arr.flagEditUse = 1;
		}

		if (obj.vars.flagRemove) {
			obj.arr.flagEditUse = 0;
			obj.arr.flagCopyUse = 0;
		}

		if (obj.vars.flagBtnAdd && obj.flagVersion) obj.arr.flagCopyUse = 1;

		return obj.arr;

	},

	/**
	 *
	*/
	_updateDetailTool : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == 'Reload') {
				obj.arr[i].varsContext.varsDetail = this._updateDetailToolReload({
					arr  : obj.vars.jsonVersion,
					data : obj.arr[i],
					vars : obj.vars
				});
			}
		}
	},

	/**
	 *
	*/
	_updateDetailToolReload : function(obj)
	{
		var arrNew = [];

		var insDisplay = new Code_Lib_TimeDisplay();

		for (var i = 0; i < obj.arr.length; i++) {
			var strVersion = 'Ver.' + (i + 1);
			var varsTmpl = (Object.toJSON(obj.data.varsContext.varsTmpl)).evalJSON();

			var objData = this.insRoot.insTimeZone.adjustDate({
				stamp : obj.arr[i].stampUpdate * 1000
			});

			var strTime = insDisplay.get({
				flagType : 3,
				vars     : objData
			});

			var str = strVersion + ' - ' + strTime;
			varsTmpl.id = varsTmpl.id + i;
			varsTmpl.strTitle = str;
			varsTmpl.vars.idTarget = obj.arr[i];
			arrNew.unshift(varsTmpl);

		}
		arrNew.unshift((Object.toJSON(obj.data.varsContext.varsTmpl)).evalJSON());

		return arrNew;
	},

	/**
	 *
	*/
	_updateDetailListVarsBtn : function(obj)
	{
		var flagCheckboxUse = obj.vars.flagCheckboxUse;
		for (var i = 0; i < obj.arr.length; i++) {
			obj.arr[i].flagUse = 0;
			if (!flagCheckboxUse) {
				continue;
			}

			if (obj.arr[i].vars.idTarget == 'delete') {
				if (obj.vars.flagBtnDelete) {
					obj.arr[i].flagUse = 1;
				}

			} else if (obj.arr[i].vars.idTarget == 'output') {
				if (obj.vars.flagBtnOutput) {
					obj.arr[i].flagUse = 1;
				}

			} else if (obj.arr[i].vars.idTarget == 'Log') {

				if (!obj.flagVersion && obj.vars.flagBtnLog) {
					obj.arr[i].flagUse = 1;
				}

			} else if (obj.arr[i].vars.idTarget == 'Cash') {

				if (!obj.flagVersion && obj.vars.flagBtnCash) {
					obj.arr[i].flagUse = 1;
				}
			}
		}

		return obj.arr;
	},

	/**
	 *
	*/
	_eventDetailVersion : function(obj)
	{
		if (obj.idTarget == 'search') {
			this._eventDetailConnect({flag : 'reload'});
			return;
		}
		var vars = (Object.toJSON(this.insDetail.varsEventList.vars)).evalJSON();
		var varsVersion = obj.vars;
		vars.strTitle = varsVersion.strTitle;
		vars.vars.strTitle = varsVersion.strTitle;
		vars.stampUpdate = varsVersion.stampUpdate;
		vars.numSize = varsVersion.numSize;
		vars.vars.numSize = varsVersion.numByte;
		vars.numHeight = varsVersion.numHeight;
		vars.numWidth = varsVersion.numWidth;
		vars.numVersion = varsVersion.numVersion;
		vars.strFileType = varsVersion.strFileType;
		vars.vars.strFileType = varsVersion.strFileType;
		vars.arrSpaceStrTag = varsVersion.arrSpaceStrTag;
		vars.vars.arrSpaceStrTag = varsVersion.vars.arrSpaceStrTag;
		vars.varsColumnDetail.strVersion = varsVersion.strVersion;
		vars.varsColumnDetail.numSize = varsVersion.numSize;
		vars.varsColumnDetail.strFileType = varsVersion.strFileType;

		this._eventDetailList({
			flagVersion : 1,
			vars        : vars
		});
	},

	/**
	 *
	*/
	_updateDetailListVarsChild : function(obj)
	{
		var insDisplay = new Code_Lib_TimeDisplay();
		var insEscape = new Code_Lib_Escape();
		var arrayNew = [];

		for (var i = 0; i < obj.arr.length; i++) {
			var id = insEscape.strLowCapitalize({data : obj.arr[i].id});
			if (obj.arr[i].id == 'StampRegister') {
				var objTime = this.insRoot.insTimeZone.adjustDate({stamp : obj.vars.stampRegister * 1000});
				obj.arr[i].value = insDisplay.get({flagType : 1, vars : objTime});
				if (!obj.vars.stampRegister) obj.arr[i].value = '-';
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'StampUpdate') {
				var objTime = this.insRoot.insTimeZone.adjustDate({stamp : obj.vars.stampUpdate * 1000});
				obj.arr[i].value = insDisplay.get({flagType : 1, vars : objTime});
				if (!obj.vars.stampUpdate) obj.arr[i].value = '-';
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'StrStatus') {
				obj.arr[i].value = obj.vars.varsColumnDetail.strStatus;
				if (obj.vars.flagRemove) {
					var objTime = this.insRoot.insTimeZone.adjustDate({stamp : obj.vars.stampRemove * 1000});
					obj.arr[i].value += '<br>( ' + insDisplay.get({flagType : 1, vars : objTime}) + ' ) ';
				}
				var temp = {};
				temp.id = obj.arr[i].id;
				temp.strTitle = obj.arr[i].value;
				temp.vars = {};
				temp.vars[id] = obj.vars.vars.flagRemove;
				temp.vars.idTarget = obj.arr[i].id;
				obj.arr[i].varsTextBtn.push(temp);
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'StrVersion') {
				obj.arr[i].value = obj.vars.varsColumnDetail.strVersion;
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'StrTitle') {
				obj.arr[i].value = obj.vars.strTitle;
				var temp = {};
				temp.id = obj.arr[i].id;
				temp.strTitle = obj.arr[i].value;
				temp.vars = {};
				temp.vars[id] = obj.vars.vars[id];
				temp.vars.idTarget = obj.arr[i].id;
				obj.arr[i].varsTextBtn.push(temp);
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'NumSize') {
				obj.arr[i].value = obj.vars.varsColumnDetail.numSize;
				var temp = {};
				temp.id = obj.arr[i].id;
				temp.strTitle = obj.arr[i].value;
				temp.vars = {};
				temp.vars[id] = obj.vars.vars[id];
				temp.vars.idTarget = obj.arr[i].id;
				obj.arr[i].varsTextBtn.push(temp);
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'StrFileType') {
				obj.arr[i].value = obj.vars.varsColumnDetail.strFileType;
				var temp = {};
				temp.id = obj.arr[i].id;
				temp.strTitle = obj.arr[i].value;
				temp.vars = {};
				temp.vars[id] = obj.vars.vars[id];
				temp.vars.idTarget = obj.arr[i].id;
				obj.arr[i].varsTextBtn.push(temp);
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'JsonChargeHistory') {
				var temp = obj.vars.jsonChargeHistory.interpolate({idSelf : this.idSelf + obj.arr[i].id});
				obj.arr[i].strHtml = temp;
				obj.arr[i].jsonChargeHistory = obj.vars.vars.jsonChargeHistory;
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'ArrSpaceStrTag') {
				obj.arr[i].value = (obj.vars.arrSpaceStrTag)? obj.vars.arrSpaceStrTag : '-';
				if (obj.arr[i].value == '-') {
					obj.arr[i].varsTextBtn = null;
					arrayNew.push(obj.arr[i]);
					continue;
				}
				for (var j = 0; j < obj.vars.vars.arrSpaceStrTag.length; j++) {
					var str = obj.vars.vars.arrSpaceStrTag[j];
					if (str === '') {
						continue;
					}
					var temp = {};
					temp.id = obj.arr[i].id + '_' + j;
					temp.strTitle = str;
					temp.vars = {};
					temp.vars.strTag = str;
					temp.vars.idTarget = obj.arr[i].id;
					obj.arr[i].varsTextBtn.push(temp);
				}
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'Id') {
				obj.arr[i].value = obj.vars.id;
				var temp = {};
				temp.id = obj.arr[i].id;
				temp.strTitle = obj.arr[i].value;
				temp.vars = {};
				temp.vars[id] = obj.vars.vars[id];
				temp.vars.idTarget = obj.arr[i].id;
				obj.arr[i].varsTextBtn.push(temp);
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'DummyImg') {
				if (obj.vars.varsColumnDetail.strFileType.match(/^(png|jpeg|jpg|gif|bmp)$/i)) {
					obj.arr[i].varsData = obj.vars;
					arrayNew.push(obj.arr[i]);
				}
			}
		}

		return arrayNew;
	},

	/**
	 *
	*/
	eventEditorSendSuccess : function(obj)
	{
		if (obj.flag == 'add' || obj.flag == 'reset') {
			this._eventListConnect({flag : 'Reload'});

		} else if (obj.flag == 'edit') {
			if (this.insDetail.varsEventList.vars.vars.idTarget == obj.idTarget) {
				this._eventDetailConnect({flag : 'reload'});
			} else {
				this._eventListConnect({flag : 'Reload'});
			}
		}
	},

	/**
	 *
	*/
	_iniChild : function(obj)
	{
		this._extChild(obj);
	},

	/**
	 *
	*/
	_varChild : function()
	{
		var vars = {};
		var insEscape = new Code_Lib_Escape();
		var str = this._varsChild.strChild;
		if (str) {
			str = insEscape.strLowCapitalize({data : str});
			if (this.vars.child[str]) {
				vars = (Object.toJSON(this.vars.child[str])).evalJSON();
			} else {
				vars = (Object.toJSON(this.vars.child.templateWindow)).evalJSON();
			}

		} else {
			vars = (Object.toJSON(this.vars.child.templateWindow)).evalJSON();
		}

		var strExt = this._varsChild.strExt;
		var strChild = this._varsChild.strChild;
		vars.id =  strExt + strChild;
		vars.strTitle = vars.strTitle.replace(/<?php echo '<%'; ?>
child<?php echo '%>'; ?>
/, this._varsChild.strTitleChild);
		vars.strTitle = vars.strTitle.replace(/<?php echo '<%'; ?>
parent<?php echo '%>'; ?>
/, this._varsChild.strTitleParent);
		this._varsChild.varsWindow[strExt + strChild] = vars;
	},

	/**
	 *
	*/
	_iniCss : function()
	{
		this._extCss();
	}
});
<?php }
}
?>