<?php /* Smarty version 3.1.24, created on 2016-08-18 13:21:16
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/plugin/accounting/js/jpn/ledger.js" */ ?>
<?php
/*%%SmartyHeaderCode:173828694357b5b64c6f12f7_82878704%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '377b515bc80d1f3f41570e7cec5c2ee9d11c83a6' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/plugin/accounting/js/jpn/ledger.js',
      1 => 1471523678,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '173828694357b5b64c6f12f7_82878704',
  'variables' => 
  array (
    'varsLoad' => 0,
    'numNews' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_57b5b64c880247_75634256',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_57b5b64c880247_75634256')) {
function content_57b5b64c880247_75634256 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '173828694357b5b64c6f12f7_82878704';
?>

/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Plugin_Accounting_Ledger = Class.create(Code_Lib_ExtPortal,
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
		this._iniPopup();
		this._iniLayout();
		this._iniNavi();
		this._iniList();
	},

	/**
	 *
	*/
	_iniVars : function(obj)
	{
		this._extVars(obj);
	},

	/**
		{
			flagFiscalPeriod  : '',
			idDepartment      : '',
			idAccountTitle    : '',
			idSubAccountTitle : '',
		}
	*/
	bootAutoSearchOver : function(obj)
	{
		var insEscape = new Code_Lib_Escape();

		var flagFiscalPeriod = 'f1';
		if (obj.flagFiscalPeriod) {
			flagFiscalPeriod = obj.flagFiscalPeriod;
		}

		this.insNavi.setFormValue({idTarget : 'FlagFiscalPeriod', value : flagFiscalPeriod});

		var idDepartment = 'none';
		if (obj.idDepartment) {
			idDepartment = obj.idDepartment;
		}
		this.insNavi.setFormValue({idTarget : 'IdDepartment', value : idDepartment});

		var idAccountTitle = obj.idAccountTitle;
		this.insNavi.setFormValue({idTarget : 'IdAccountTitle', value : idAccountTitle});

		this._setNaviFormSelectIdAccountTitle({
			arr : this.insNavi.insForm.vars.varsDetail
		});

		var idSubAccountTitle = 'none';
		if (obj.idSubAccountTitle) {
			idSubAccountTitle = obj.idSubAccountTitle;
		}
		this.insNavi.setFormValue({idTarget : 'IdSubAccountTitle', value : idSubAccountTitle});

		this.insNavi.hideBtnBottom();
		this._eventNaviConnect({flag : 'search'});
	},

	/**
	 *
	*/
	_idLog : 'Log',
	_varsAutoData : {},
	_checkAutoSearch : function(obj)
	{
		var insEscape = new Code_Lib_Escape();
		this._varsAutoData = {
			vars       : {
				vars : {
					idTarget : 'idLog',
					idLog    : obj.idTarget
				}
			},
			flagDetail : 1
		};

		var varsLogData = this.insTop.checkChildData({idTarget : this._idLog});
		if (!varsLogData) {
			var idTarget = insEscape.strLowCapitalize({data : this._idLog});
			this.insTop.iniAutoBoot({
				idTarget     : idTarget + 'Window',
				insBack      : this,
				strBackFunc  : 'eventAutoSearch'
			});

		} else {
			if (varsLogData.insWindow.vars.flagHideNow) {
				varsLogData.insWindow.updateHide({ flagEffect : 1 });
			} else {
				varsLogData.insWindow.setZIndex();
			}
			this.eventAutoSearch();
		}
	},

	eventAutoSearch : function()
	{
		var varsLogData = this.insTop.checkChildData({idTarget : this._idLog});
		varsLogData.insClass.bootAutoSearchOver(this._varsAutoData);
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
						strTitleParent  : insCurrent.insWindow.vars.strTitle,
						strTitleChild   : insCurrent.vars.child.varsTitle[obj.vars.id],
						strExt          : obj.vars.id,
						strChild        : '',
						strClass        : insCurrent.strClass,
						idModule        : insCurrent.idModule
					});
				}

			} else if (obj.from == 'navi-_mousedownMenu') {
				return insCurrent.insNavi.vars.varsStatus.flagNow;

			} else if (obj.from == 'navi-_mousedownLine') {
				insCurrent.insNavi.eventTool({idTarget : obj.vars});

			} else if (obj.from == 'list-_mousedownNavi') {
				if (obj.vars.id == 'Switch') {
					insCurrent.insList.eventTool({idTarget : insCurrent.insList.vars.varsStatus.flagNow, flagLoop:1});

				} else if (obj.vars.id == 'Reload') {
					insCurrent._preEventLayout({flag : 'reset'});
					insCurrent._eventListConnect({flag : obj.vars.id});

				} else if (obj.vars.id == 'Output') {
					insCurrent._eventListConnect({flag : obj.vars.id, flagType : insCurrent.insList.vars.varsStatus.flagOutputNow});

				} else if (obj.vars.id == 'Print') {
					insCurrent._eventListConnect({flag : obj.vars.id, flagType : insCurrent.insList.vars.varsStatus.flagPrintNow});
				}

			} else if (obj.from == 'list-_mousedownMenu') {
				if (obj.vars.id == 'Switch') {
					return insCurrent.insList.vars.varsStatus.flagNow;

				} else if (obj.vars.id == 'Output') {
					return insCurrent.insList.vars.varsStatus.flagOutputNow;

				} else if (obj.vars.id == 'Print') {
					return insCurrent.insList.vars.varsStatus.flagPrintNow;
				}

			} else if (obj.from == 'list-_mousedownLine') {
				if (obj.varsTarget == 'Switch') {
					return insCurrent.insList.eventTool({idTarget : obj.vars});

				} else if (obj.varsTarget) {
					insCurrent.insList.eventTool({idTarget : obj.vars, flagStr : obj.varsTarget});
					insCurrent._eventListConnect({flag : obj.varsTarget, flagType : obj.vars});
					return;
				}

			} else if (obj.from == 'detail-_mousedownNavi') {

				if (obj.vars.id == 'Reload') {
					insCurrent._preEventLayout({flag : 'reset'});
					insCurrent._eventDetailConnect({flag : 'reload'});

				} else if (obj.vars.id == 'Output') {
					insCurrent._eventDetailConnect({flag : 'output'});

				} else if (obj.vars.id == 'Add'
						|| obj.vars.id == 'Copy'
						|| obj.vars.id == 'Edit'
				) {
					insCurrent._setDetailChild({flag : obj.vars.id.toLowerCase()});

				} else if (obj.vars.id == 'Preference') {
					insCurrent._iniChild({
						strTitleParent  : insCurrent.insWindow.vars.strTitle,
						strTitleChild   : insCurrent.vars.child.varsTitle[obj.vars.id],
						strExt          : obj.vars.id,
						flagMenuShowUse : 1,
						strChild        : '',
						strClass        : insCurrent.strClass,
						idModule        : insCurrent.idModule
					});
				}
			}
		};

		return allot;
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
	_iniNavi : function()
	{
		this._setNavi();
		this._setNaviStart();
		this._eventValue({
			vars     : this.insNavi.getFormValue(),
			idTarget : ''
		});
	},

	/**
	 *
	*/
	insNavi : null,
	_setNavi : function()
	{
		this.insNavi = new Code_Lib_ControlDetail({
			insUnder   : this.insLayout.insNaviUnder,
			insTool    : this.insLayout.insNaviTool,
			insRoot    : this.insRoot,
			insCurrent : this,
			idSelf     : this.idSelf + 'Navi',
			allot      : this._getNaviAllot(),
			vars       : this.vars.portal.varsNavi
		});
	},

	/**
	 *
	*/
	_getNaviAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;
			if (obj.from == 'eventRemove-detail') insCurrent._eventRemoveNaviContent();
			else if (obj.from == 'form-preEventLayout') insCurrent._preEventLayoutNaviContent();
			else if (obj.from == 'form-eventLayout') insCurrent._eventLayoutNaviContent();
			else if (obj.from == 'form-eventBtnBottom') {
				insCurrent._eventNaviConnect({flag : obj.vars.vars.vars.idTarget});
			}
		};

		return allot;
	},

	/**
	 *
	*/
	_eventRemoveNaviContent : function()
	{

	},

	/**
	 *
	*/
	_preEventLayoutNaviContent : function()
	{

	},

	/**
	 *
	*/
	_eventLayoutNaviContent : function()
	{
		this._iniNaviFormSelect();
	},

	/**
	 *
	*/
	_setNaviStart : function()
	{
		var tmplDetail = (Object.toJSON(this.vars.portal.varsNavi.templateDetail)).evalJSON();
		var tmplBtn = (Object.toJSON(this.vars.portal.varsNavi.varsBtn)).evalJSON();

		this.insNavi.eventList({
			flagMoveUse : 0,
			strTitle    : this.vars.portal.varsNavi.varsStart.strTitle,
			strClass    : this.vars.portal.varsNavi.varsStart.strClass,
			vars        : {
				varsDetail : tmplDetail,
				varsBtn    : tmplBtn,
				varsEdit   : this.vars.portal.varsNavi.varsStart.varsEdit,
				vars       : {}
			}
		});
		this._setNaviContent();
	},


	/**
	 *
	*/
	_setNaviContent : function()
	{
		this._iniNaviFormSelect();

	},

	/**
	 *
	*/
	_iniNaviFormSelect : function()
	{
		this._setNaviFormSelect({arr : this.insNavi.insForm.vars.varsDetail});
		this._setNaviFormSelectIdAccountTitle({
			arr      : this.insNavi.insForm.vars.varsDetail,
			flagIni  : 1
		});
	},

	/**
	 *
	*/
	_varsNaviFormSelect : {},
	_setNaviFormSelect : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == 'IdAccountTitle') {
				var strFunc = '_getNaviFormSelect' + obj.arr[i].id +'Allot';
				var insFormSelect = new Code_Lib_FormSelect({
					eleInsert  : $(this.insNavi.insForm.idSelf + obj.arr[i].id),
					insRoot    : this.insRoot,
					insCurrent : this,
					idSelf     : this.idSelf + 'FormSelect' + obj.arr[i].id,
					allot      : this[strFunc](),
					vars       : null
				});

				this._varsNaviFormSelect[obj.arr[i].id] = insFormSelect;
			}

		}
	},

	/**
	 *
	*/
	_getNaviFormSelectIdAccountTitleAllot : function()
	{
		var allot = function(obj) {
			var insCurrent = obj.insCurrent;
			insCurrent._setNaviFormSelectIdAccountTitle({
				arr : insCurrent.insNavi.insForm.vars.varsDetail
			});
		};

		return allot;
	},

	/**
	 *
	*/
	_setNaviFormSelectIdAccountTitle : function(obj)
	{
		if (!obj.flagIni) {
			this.insNavi.setValue();
		}
		var idAccountTitle = '';
		var arrSelectTag = (Object.toJSON(this.vars.varsRule.arrSubAccountTitle.arrSelectTag)).evalJSON();
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == 'IdAccountTitle') {
				idAccountTitle = obj.arr[i].value;

			} else if (obj.arr[i].id == 'IdSubAccountTitle') {
				var varsNone = (Object.toJSON(obj.arr[i].varsTmpl.varsNone)).evalJSON();

				if (!this.vars.varsRule.arrSubAccountTitle.arrStrTitle[idAccountTitle]) {
					obj.arr[i].arrayOption = [varsNone];

				} else {
					arrSelectTag[idAccountTitle].unshift(varsNone);
					obj.arr[i].arrayOption = arrSelectTag[idAccountTitle];
					if (!obj.flagIni) {
						obj.arr[i].value = 'none';
					}
				}

				$(this.insNavi.insForm.idSelf + obj.arr[i].id).innerHTML = '';
				this.insNavi.insForm.setTemplateSelect({
					arr       : obj.arr[i].arrayOption,
					now       : obj.arr[i].value,
					eleInsert : $(this.insNavi.insForm.idSelf + obj.arr[i].id),
					vars      : obj.arr[i]
				});
			}
		}
	},

	/**
	 *
	*/
	_eventNaviConnect : function(obj)
	{
		if (obj.flag == 'search') {
			if (this.insNavi.checkForm({flagType : 'common'})) return;
			this._eventValue({
				vars     : this.insNavi.getFormValue(),
				idTarget : ''
			});
			this._eventSearch({
				numLotNow : 0,
				ph : {
					arrWhere : [],
					arrOrder : {}
				}
			});
		}

		this._varsNaviConnect = obj;
		this._sendNaviConnect();
	},

	/**
	 *
	*/
	_varsNaviConnect : null,
	_sendNaviConnect : function(obj)
	{
		var strExt = this.strExt;
		var strClass = this.strClass;
		var idModule = this.idModule;
		var strChild = this.strChild;
		var jsonSearch = Object.toJSON(this._varsSearch);
		var jsonValue = Object.toJSON(this._varsValue);
		var arrayKey = [], arrayValue = [];
		var jsonStamp = {};

		var strFunc = 'Navi' + this._varsNaviConnect.flag.capitalize();
		jsonStamp = this._getJsonStamp({strFunc : strFunc});
		arrayKey = ['class', 'module', 'ext', 'child', 'func', 'db', 'jsonStamp', 'jsonValue', 'jsonSearch'];
		arrayValue = [strClass, idModule, strExt, strChild, strFunc, 'slave', jsonStamp, jsonValue, jsonSearch];

		if (this._varsNaviConnect.flag == 'output') {
			this.insRoot.setOutput({
				querysKey       : arrayKey,
				querysValue     : arrayValue,
			});
			this.insNavi.showBtnBottom();

		} else {
			this.insRoot.insRequest.set({
				flagLock        : 0,
				numZIndex       : this.insRoot.getZIndex(),
				insCurrent      : this,
				flagEscape      : 1,
				path            : this.insRoot.vars.varsSystem.path.post,
				querysKey       : arrayKey,
				querysValue     : arrayValue,
				functionSuccess : '_sendNaviConnectSuccess',
				functionFail    : '_sendNaviConnectFail',
				eleLoadStatus   : this.insNavi.insUnder.eleFormat.header.down('.codeLibBaseMarginRightFive', 0)
			});
		}
	},


	/**
	 *
	*/
	_sendNaviConnectSuccess : function(obj)
	{
		if (obj.response.responseText.isJSON()) {
			var json = obj.response.responseText.evalJSON();
			if (json.stamp) {
				if (json.stamp.id) this._varsStamp[json.stamp.id] = json.stamp.stamp;
			}
			this.insNavi.showBtnBottom();
			if (json.flag) {
				if (json.numNews) this.insRoot.iniPopup({flag : 'news', numNews : json.numNews});
				this._eventNaviConnectSuccess({json : json});
			}
			else if (json.flag == 0) alert(this.insRoot.vars.varsSystem.str.errorSession);
		}
		else alert(this.insRoot.vars.varsSystem.str.errorRequest);
	},

/*
	/**
	 *
	*/
	_eventNaviConnectSuccess : function(obj)
	{
		if (obj.json.flag == 1) {
			if (this._varsNaviConnect.flag == 'search') {
				this.insList.resetVars({vars : obj.json.data, numLotNow : this._varsSearch.numLotNow});
				this.insList.eventNavi({
					strTitle : null,
					strClass : null,
					varsEdit : this._getNaviConnectSuccessVarsEdit()
				});

			}

		} else if (obj.json.flag == 10) {
			this.insNavi.showBtnBottom();

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
	_getNaviConnectSuccessVarsEdit : function()
	{
		var varsEdit = (Object.toJSON(this.insList.vars.varsEdit)).evalJSON();
		var idAccountTitle = this._varsValue.vars.IdAccountTitle;
		varsEdit.flagPrintUse = 1;
		varsEdit.flagOutputUse = 1;
		if (parseFloat(this.vars.varsRule.arrAccountTitle.arrStrTitle[idAccountTitle].flagUse) == 0) {
			varsEdit.flagPrintUse = 0;
			varsEdit.flagOutputUse = 0;
		}
		if (!parseFloat(this.vars.portal.varsList.varsStart.varsEdit.flagPrintUse)) {
			varsEdit.flagPrintUse = 0;
		}
		if (!parseFloat(this.vars.portal.varsList.varsStart.varsEdit.flagOutputUse)) {
			varsEdit.flagOutputUse = 0;
		}

		return varsEdit;
	},

	/**
	 *
	*/
	_eventListConnectSuccess : function(obj)
	{
		if (obj.json.flag == 1){
			if (this._varsListConnect.flag == 'Search'
				|| this._varsListConnect.flag == 'Reload'
			) {
				this.insLayout.updateTool({flagLock : 0, from : 'list', idTarget : 'Reload'});
				this.insList.resetVars({vars : obj.json.data, numLotNow : this._varsSearch.numLotNow});
				this.insList.eventNavi({
					strTitle : null,
					strClass : null,
					varsEdit : this._getNaviConnectSuccessVarsEdit()
				});

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
	_iniList : function()
	{
		this._extList();
	},

	/**
	 *
	*/
	_eventListConnect : function(obj)
	{
		if (obj.flag == 'Reload') {
			if (this.insNavi.checkForm({flagType : 'common'})) return;
			this._eventValue({
				vars     : this.insNavi.getFormValue(),
				idTarget : ''
			});

		} else if (obj.flag == 'Print' || obj.flag == 'Output') {
			var vars = this.insNavi.getFormValue();
			vars.FlagType = obj.flagType;
			this._eventValue({
				vars     : vars,
				idTarget : ''
			});

		} else if (obj.flag == 'Search') {
			this._eventValue({
				vars     : this.insNavi.getFormValue(),
				idTarget : ''
			});
			this._eventSearch({
				numLotNow : obj.vars.numLotNow,
				ph : {
					arrWhere : [],
					arrOrder : {}
				}
			});
		}

		this._varsListConnect = obj;
		this._sendListConnect();
	},

	_eventSearch : function(obj)
	{
		var jsonNext = Object.toJSON(obj.ph);
		var jsonBefore = Object.toJSON(this._varsSearch.ph);

		if (jsonNext == jsonBefore) {
			this._varsSearch.flagReload = 0;
			if (obj.numLotNow == this._varsSearch.numLotNow) this._varsSearch.flagReload = 1;
			this._varsSearch.numLotNow = obj.numLotNow;

		} else {
			this._varsSearch.numLotNow = 0;
			this._varsSearch.flagReload = 0;
		}
		this._varsSearch.ph = obj.ph;
	},

	/**
	 *
	*/
	_getListAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;
			var array = obj.from.split('-');
			if (array[0] == 'table') {
				if (array[1] == '_mousedownMove') insCurrent.insNavi.eventMove({vars : obj.vars});
				else if (array[1] == '_dblclickBtn') insCurrent._eventDetailList({vars : obj.vars});
				else if (array[1] == 'eventPage') insCurrent._eventListConnect({vars : obj.vars, flag : 'Search'});
				else if (array[1] == 'eventBtnBottom') insCurrent._eventListConnect({flag : obj.vars.vars.vars.idTarget});

			} else if (array[0] == 'schedule') {
				if (array[1] == 'eventPage') insCurrent._eventListConnect({vars : obj.vars, flag : 'Search'});
				else if (array[1] == '_mousedownLogBtn') insCurrent._eventDetailList({vars : obj.vars.detailLog});
				else if (array[1] == 'mousedownMove') insCurrent.insNavi.eventMove({vars : obj.vars});

			} else if (array[0] == 'tableTree') {
				if (array[1] == '_mousedownBtn') insCurrent._eventDetailList({vars : obj.vars[0]});
				else if (array[1] == 'eventPage') insCurrent._eventListConnect({vars : obj.vars, flag : 'Search'});
				else if (array[1] == 'eventBtnBottom') insCurrent._eventListConnect({flag : obj.vars.vars.vars.idTarget});

			} else if (array[0] == 'tree') {
				if (array[1] == '_mousedownBtn') insCurrent._eventDetailList({vars : obj.vars});
				else if (array[1] == 'eventPage') insCurrent._eventListConnect({vars : obj.vars, flag : 'Search'});
				else if (array[1] == 'eventBtnBottom') insCurrent._eventListConnect({flag : obj.vars.vars.vars.idTarget});

			} else if (array[0] == 'resetVars') {
				insCurrent._checkListBtn({
					varsDetail : obj.vars.varsDetail,
					varsBtn    : obj.vars.varsBtn
				});
				insCurrent._checkListToolEdit({
					varsDetail : obj.vars.varsDetail,
					varsEdit   : obj.vars.varsEdit
				});
			}
		};

		return allot;
	},

	/**
	 *
	*/
	_eventDetailList : function(obj)
	{
		if (this.vars.flagAuthorityLog) {
			this._checkAutoSearch({idTarget : obj.vars.varsColumnDetail.idLog});
		}
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