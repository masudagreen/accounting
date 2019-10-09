<?php /* Smarty version 3.1.24, created on 2019-07-06 09:28:46
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/plugin/accounting/js/jpn/trialBalance.js" */ ?>
<?php
/*%%SmartyHeaderCode:1410009335d2069cea063a6_84705976%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '11abc58f7a55fff853c4b99e45f8065cc82367ee' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/plugin/accounting/js/jpn/trialBalance.js',
      1 => 1560675145,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1410009335d2069cea063a6_84705976',
  'variables' => 
  array (
    'varsLoad' => 0,
    'numNews' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_5d2069cea7d411_18610285',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_5d2069cea7d411_18610285')) {
function content_5d2069cea7d411_18610285 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '1410009335d2069cea063a6_84705976';
?>

/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Plugin_Accounting_TrialBalance = Class.create(Code_Lib_ExtPortal,
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
		this._iniNavi();
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
	 *
	*/
	_idLedger : 'Ledger',
	eventAutoSearch : function()
	{
		var varsLedgerData = this.insTop.checkChildData({idTarget : this._idLedger});
		varsLedgerData.insClass.bootAutoSearchOver(this._varsAutoData);
	},

	/**
	 *
	*/
	_varsAutoData : {},
	_checkAutoSearch : function(obj)
	{
		var insEscape = new Code_Lib_Escape();
		var varsValue = this.insNavi.getFormValue();
		this._varsAutoData = {
			flagFiscalPeriod  : obj.flagFiscalPeriod,
			idDepartment      : obj.idDepartment,
			idAccountTitle    : obj.idAccountTitle,
			idSubAccountTitle : obj.idSubAccountTitle
		};

		var varsLedgerData = this.insTop.checkChildData({idTarget : this._idLedger});
		if (!varsLedgerData) {
			var idTarget = insEscape.strLowCapitalize({data : this._idLedger});
			this.insTop.iniAutoBoot({
				idTarget     : idTarget + 'Window',
				insBack      : this,
				strBackFunc  : 'eventAutoSearch'
			});

		} else {
			if (varsLedgerData.insWindow.vars.flagHideNow) {
				varsLedgerData.insWindow.updateHide({ flagEffect : 1 });
			} else {
				varsLedgerData.insWindow.setZIndex();
			}
			this.eventAutoSearch();
		}
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
			if (obj.from == 'form-preEventLayout') insCurrent._preEventLayoutNaviContent();
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
	_preEventLayoutNaviContent : function()
	{

	},

	/**
	 *
	*/
	_eventLayoutNaviContent : function()
	{

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
					arrWhere : {},
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
		var arrayKey = [];
		var arrayValue = [];
		var jsonStamp = {};

		var strFunc = 'Navi' + this._varsNaviConnect.flag.capitalize();
		jsonStamp = this._getJsonStamp({strFunc : strFunc});
		arrayKey = ['class', 'module', 'ext', 'child', 'func', 'db', 'jsonStamp', 'jsonValue', 'jsonSearch'];
		arrayValue = [strClass, idModule, strExt, strChild, strFunc, 'slave', jsonStamp, jsonValue, jsonSearch];

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


	/**
	 *
	*/
	_eventNaviConnectSuccess : function(obj)
	{
		if (obj.json.flag == 1) {
			if (this._varsNaviConnect.flag == 'search') {
				this.insList.updateTableTreeVars({vars : obj.json.data});
				this.vars.varsFlag = obj.json.data.varsFlag;
				this.vars.varsItem.varsSubValue = obj.json.data.varsSubValue;
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

	/**
	 *
	*/
	eventListConnect : function(obj)
	{
		this._eventListConnect({flag : 'Reload'});
	},

	/**
	 *
	*/
	_eventListConnectSuccess : function(obj)
	{
		if (obj.json.flag == 1){
			if (this._varsListConnect.flag == 'Reload') {
				this.insLayout.updateTool({flagLock : 0, from : 'list', idTarget : 'Reload'});
				this.insList.updateTableTreeVars({vars : obj.json.data});
				this.vars.varsFlag = obj.json.data.varsFlag;
				this.vars.varsItem.varsSubValue = obj.json.data.varsSubValue;

			} else if (this._varsListConnect.flag == 'Print' ) {
				this.insRoot.setPrint({data : obj.json.data, insCurrent : this, strFunc : 'eventListConnectSuccessPrint'});
			}

		} else if (obj.json.flag == 10) {
			if (this._varsListConnect.flag == 'Reload') {
				this.insLayout.updateTool({flagLock : 0, from : 'list', idTarget : 'Reload'});
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
	_getListAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;
			var array = obj.from.split('-');
			if (array[0] == 'table') {
				if (array[1] == '_mousedownMove') insCurrent.insNavi.eventMove({vars : obj.vars});

				else if (array[1] == 'eventPage') insCurrent._eventListConnect({vars : obj.vars, flag : 'Search'});
				else if (array[1] == 'eventBtnBottom') insCurrent._eventListConnect({flag : obj.vars.vars.vars.idTarget});

			} else if (array[0] == 'schedule') {
				if (array[1] == 'eventPage') insCurrent._eventListConnect({vars : obj.vars, flag : 'Search'});
				else if (array[1] == '_mousedownLogBtn') insCurrent._eventDetailList({vars : obj.vars.detailLog});
				else if (array[1] == 'mousedownMove') insCurrent.insNavi.eventMove({vars : obj.vars});

			} else if (array[0] == 'tableTree') {
				if (array[1] == '_dblclickBtn') insCurrent._eventDetailListLedger({vars : obj.vars});
				else if (array[1] == '_mousedownBtn') insCurrent._eventDetailList({vars : obj.vars[0]});
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

	_eventDetailListLedger : function(obj)
	{
		if (this.vars.flagAuthorityLedger) {
			var varsValue = this.insNavi.getFormValue();
			this._checkAutoSearch({
				flagFiscalPeriod  : varsValue.FlagFiscalPeriod,
				idDepartment      : varsValue.IdDepartment,
				idAccountTitle    : obj.vars.vars.idTarget,
				idSubAccountTitle : ''
			});
		}
	},

	/**
	 *
	*/
	_eventDetailList : function(obj)
	{
		var objData = this._updateDetailListVars({vars : obj.vars});
		this.insDetail.eventList(objData);
		this._setDetailContent({vars : obj.vars});
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
	_setDetailStart : function()
	{
		this.insDetail.eventList({
			strTitle    : this.vars.portal.varsDetail.varsStart.strTitle,
			strClass    : this.vars.portal.varsDetail.varsStart.strClass,
			vars        : {
				varsDetail : [],
				varsBtn    : [],
				varsEdit   : {},
				vars       : {}
			}
		});
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
			else if (obj.from == 'view-eventLayout') insCurrent._eventLayoutDetailContent();
			else if (obj.from == 'view-checkTextBtn') insCurrent._checkDetailContentTextBtn({vars : obj.vars});
			else if (obj.from == 'view-eventBtnBottom') insCurrent._eventDetailConnect({flag : obj.vars.vars.vars.idTarget});
		};

		return allot;
	},



	/**
	 *
	*/
	_varsContent : {num : 0},
	_setDetailContent : function(obj)
	{
		this._varsContent.num = 0;
		this._iniDetailSpace();
	},

	/**
	 *
	*/
	_varsDetailSpace : {},
	_iniDetailSpace : function()
	{
		this._varsDetailSpace = {};
		this._setDetailSpace({arr : this.insDetail.insView.vars.varsDetail});
	},

	/**
	 *
	*/
	_setDetailSpace : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].varsSpace) continue;
			var ele = this.insDetail.insView.eleWrap.down('.codeLibViewLineContent', this._varsContent.num);
			this._varsContent.num++;
			if (obj.arr[i].id == 'TableDetail') {
				this._setDetailViewTableDetail({
					vars : obj.arr[i]
				});
			}
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
			if (obj.arr[i].id == 'TableDetail') {
				this._setDetailViewTableDetailTextBtn({
					vars : obj.arr[i]
				});
			}
		}
	},

	/**
	 *
	*/
	_setDetailViewTableDetailTextBtn : function(obj)
	{
		var flagFiscalPeriod = this.vars.varsFlag.flagFiscalPeriod;
		var idDepartment = this.vars.varsFlag.idDepartment;
		var idAccountTitle = this.insDetail.varsEventList.vars.vars.idTarget;
		var arr = this.vars.varsItem.arrSubAccountTitle.arrSelectTag[idAccountTitle];
		if (arr == undefined) {
			return;
		}
		for (var i = 0; i < arr.length; i++) {
			var idSubAccountTitle = arr[i].value + '';
			var insBtn = new Code_Lib_Btn();
			var temp = {};
			temp.id = idSubAccountTitle;
			temp.strTitle = arr[i].strTitle;
			temp.vars = {};
			temp.vars.idDepartment = idDepartment;
			temp.vars.flagFiscalPeriod = flagFiscalPeriod;
			temp.vars.idAccountTitle = idAccountTitle;
			temp.vars.idSubAccountTitle = idSubAccountTitle;
			insBtn.iniBtnTextTarget({
				eleInsert  : $(this.idSelf + '_' + idSubAccountTitle),
				id         : this.idSelf + 'Btn' + idSubAccountTitle,
				strFunc    : '_checkDetailContentTextBtn',
				strTitle   : arr[i].strTitle,
				insCurrent : this,
				vars       : temp
			});
			this._setListener({ins : insBtn});
		}

	},

	/**
	 *
	*/
	_varsDetailList : [],
	_setDetailViewTableDetail : function(obj)
	{

		var varsTmpl = obj.vars.tmplVars;
		var flagFiscalPeriod = this.vars.varsFlag.flagFiscalPeriod;
		var flagFS = this.vars.varsFlag.flagFS;
		var idDepartment = this.vars.varsFlag.idDepartment;
		if (idDepartment == 0 || idDepartment == 'none') {
			idDepartment = 'all';
		} else {
			idDepartment += '';
		}
		var idAccountTitle = this.insDetail.varsEventList.vars.vars.idTarget;

		var strContent = '';
		var strColumn = '';
		var arrStr = ['strTitle', 'strPrev', 'strDebit', 'strCredit', 'strNext'];
		for (var i = 0; i < arrStr.length; i++) {
			var tagTdColumn = obj.vars.tagTdColumn;
			strColumn += tagTdColumn.interpolate({insertPoint : obj.vars.varsStr[arrStr[i]], numWidth : obj.vars.varsSpace.varsStatus.numWidth});
		}
		tagTdColumn = obj.vars.tagTdColumn;
		var strRate = obj.vars.varsStr.strRatePLCR;
		if (flagFS == 'BS') {
			strRate = obj.vars.varsStr.strRateBS;
		}
		strColumn += tagTdColumn.interpolate({insertPoint : strRate, numWidth : obj.vars.varsSpace.varsStatus.numWidth});

		var tagTr = obj.vars.tagTr;
		strColumnTr = tagTr.interpolate({insertPoint : strColumn});
		strContent = strColumnTr;
		this._varsDetailList = [];
		var arr = this.vars.varsItem.arrSubAccountTitle.arrSelectTag[idAccountTitle];
		if (arr == undefined) {
			obj.vars.varsSpace.varsDetail.strHtml = this.vars.varsItem.strNone;
			return;
		}
		var arrStr = ['sumPrev', 'sumDebit', 'sumCredit', 'sumNext'];
		for (var i = 0; i < arr.length; i++) {

			var strRow = '';
			var idSubAccountTitle = arr[i].value + '';

			var varsNum = this._getDetailViewTableDetailVarsValue({
				flagFiscalPeriod  : flagFiscalPeriod,
				flagFS            : flagFS,
				idDepartment      : idDepartment,
				idSubAccountTitle : idSubAccountTitle
			});

			var tagTdRow = obj.vars.tagTdRow;
			strRow += tagTdRow.interpolate({insertPoint : '', id: this.idSelf + '_' + idSubAccountTitle});

			for (var j = 0; j < arrStr.length; j++) {
				tagTdRow = obj.vars.tagTdRowRight;
				var str = varsNum[arrStr[j] + 'Comma'];
				strRow += tagTdRow.interpolate({insertPoint : str});
			}
			tagTdRow = obj.vars.tagTdRowRight;
			var str = varsNum.numRate;
			strRow += tagTdRow.interpolate({insertPoint : str});

			var tagTr = obj.vars.tagTr;
			strColumnTr = tagTr.interpolate({insertPoint : strRow});
			strContent += strColumnTr;
		}

		var tagTable = obj.vars.tagTable;
		var strTable = tagTable.interpolate({insertPoint : strContent});
		obj.vars.varsSpace.varsDetail.strHtml = strTable;
	},

	/**
	 *
	*/
	_checkDetailContentTextBtn : function(obj)
	{
		if (this.vars.flagAuthorityLedger) {
			this._checkAutoSearch({
				flagFiscalPeriod  : obj.vars.vars.vars.flagFiscalPeriod,
				idDepartment      : obj.vars.vars.vars.idDepartment,
				idAccountTitle    : obj.vars.vars.vars.idAccountTitle,
				idSubAccountTitle : obj.vars.vars.vars.idSubAccountTitle
			});
		}
	},

	/**
	 *
	*/
	_getDetailViewTableDetailVarsValue : function(obj)
	{
		var varsSubValue = this.vars.varsItem.varsSubValue;

		var data = {
			sumPrev        : 0,
			sumDebit       : 0,
			sumCredit      : 0,
			sumNext        : 0,
			numRate        : '0.000',
			sumPrevComma   : '0',
			sumDebitComma  : '0',
			sumCreditComma : '0',
			sumNextComma   : '0'
		};
		if (obj.flagFS != 'BS') {
			data.sumPrev = '-';
			data.sumPrevComma = '-';
		}

		if (!varsSubValue) {
			 return data;
		}
		if (!varsSubValue[obj.idSubAccountTitle]) {
			 return data;
		}
		if (!varsSubValue[obj.idSubAccountTitle][obj.idDepartment]) {
			 return data;
		}

		return varsSubValue[obj.idSubAccountTitle][obj.idDepartment];
	},

	/**
	 *
	*/
	_eventLayoutDetailContent : function()
	{
		if (!this.insDetail.varsEventList) return;
		this._varsContent.num = 0;
		this._iniDetailSpace();
	},

	/**
	 *
	*/
	_preEventLayoutDetailContent : function()
	{
		if (!this.insDetail.varsEventList) return;

	},

	/**
	 *
	*/
	_eventRemoveDetailContent : function()
	{
		if (!this.insDetail.varsEventList) return;
		this.stopListener();
	},



	/**
	 *
	*/
	_updateDetailListVars : function(obj)
	{
		var objDetail = (Object.toJSON(this.vars.portal.varsDetail.templateDetail)).evalJSON();
		var objData = {
			strTitle    : this.insDetail.vars.varsStart.strTitle,
			strClass    : obj.vars.strClass,
			vars        : {
				varsDetail : this._updateDetailListVarsChild({arr : objDetail, vars : obj.vars }),
				varsBtn    : [],
				varsEdit   : this.insDetail.vars.view.varsEdit,
				vars       : obj.vars
			}
		};

		return objData;
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
			if (obj.arr[i].id == 'TableDetail') {
				arrayNew.push(obj.arr[i]);
			}
		}

		return arrayNew;
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
	_iniCss : function()
	{
		this._extCss();
	}
});
<?php }
}
?>