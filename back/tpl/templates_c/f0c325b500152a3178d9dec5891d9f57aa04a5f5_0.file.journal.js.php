<?php /* Smarty version 3.1.24, created on 2022-08-13 00:24:08
         compiled from "/var/www/html/accounting/back/tpl/templates/else/plugin/accounting/js/lib/journal.js" */ ?>
<?php
/*%%SmartyHeaderCode:106969484862f6ef28b3ed17_78095471%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f0c325b500152a3178d9dec5891d9f57aa04a5f5' => 
    array (
      0 => '/var/www/html/accounting/back/tpl/templates/else/plugin/accounting/js/lib/journal.js',
      1 => 1569243562,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '106969484862f6ef28b3ed17_78095471',
  'variables' => 
  array (
    'varsLoad' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_62f6ef28c0ffb2_96957003',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_62f6ef28c0ffb2_96957003')) {
function content_62f6ef28c0ffb2_96957003 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '106969484862f6ef28b3ed17_78095471';
?>

/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Plugin_Accounting_Lib_Journal = Class.create(Code_Lib_ExtLib,
{

	varsLoad : <?php echo $_smarty_tpl->tpl_vars['varsLoad']->value;?>
,


	/**
	 *
	 */
	initialize : function(obj)
	{
		this._iniVarsLoad(obj);
	},

	iniLoad : function(obj)
	{
		this._extAllot(obj);
		this._iniVars(obj);
		this._iniListener();
		this._iniWrap();
		this._iniBtnInput();
		this._iniSummary();
		this._iniDetail();
		if (!this.vars.varsDetail.varsDetail.length) {
			this._mousedownBarAdd();
		}
	},

	/**
	 *
	 */
	_iniBtnInput : function(obj)
	{
		if (this.vars.varsStatus.flagEditUse) {
			this._setBtnWrap();
			this._iniBtnDictionary();
			this._iniBtnTitle();
		}
	},

	_eleBtnWrap : null,
	_setBtnWrap : function(obj)
	{
		var ele = $(document.createElement('span'));
		this._eleBtnWrap = ele;
		this.eleWrap.insert(this._eleBtnWrap);
		this._eleBtnWrap.setStyle({
			width  : this._getWrapWidth() + 'px'
		});

		return ele;
	},

	/**
	 *
	*/
	_iniBtnTitle : function(obj)
	{
/*
 * xxxxxx
 */
return;
		if (!this.vars.varsStatus.flagBtnTitleUse) {
			return;
		}
		this._setBtnTitleWrap();
		this._setBtnTitle();
	},

	_eleBtnTitleWrap : null,
	_setBtnTitleWrap : function(obj)
	{
		var ele = $(document.createElement('span'));
		this._eleBtnTitleWrap = ele;
		this._eleBtnTitleWrap.addClassName('codePluginAccountingLibBtnTitleWrap');
		this.eleWrap.insert(this._eleBtnTitleWrap);
		var numPadding = 5;
		this._eleBtnTitleWrap.setStyle({
			width         : (this._getWrapWidth() - numPadding) + 'px',
			paddingLeft   : numPadding  + 'px',
			paddingBottom : numPadding  + 'px'
		});

		return ele;
	},

	/**
	 *
	*/
	_setBtnTitle : function()
	{
		var insBtn = new Code_Lib_Btn();
		var id = this.idSelf + 'BtnTitle';
		insBtn.iniBtn({
			eleInsert  : this._eleBtnWrap,
			id         : id,
			strFunc    : '_mousedownBtnTitle',
			strTitle   : this.vars.varsTmpl.strBtnTitle,
			insCurrent : this,
			flagATag   : null,
			path       : null,
			vars       : {}
		});
		this._setListener({ins : insBtn});
		var ele = $(id);
		ele.setStyle({
			marginBottom : '5px',
			marginRight  : '5px'
		});
	},

	/**
	 *
	*/
	_mousedownBtnTitle : function(obj)
	{
		this.allot({
			insCurrent : this.insCurrent,
			from       : '_mousedownBtnTitle'
		});
	},

	/**
	 *
	*/
	_resetBtnTitle : function()
	{
		var arr = this._arrBtnTitleIns;
		for (var i = 0; i < arr.length; i++) {
			if (arr[i].insListener) {
				arr[i].insListener.stop();
			}
		}
		this._arrBtnTitleIns = [];
		this._eleBtnTitleWrap.innerHTML = '';
	},

	/**
		{
			strTitle : '',
			arr : [
				{
					strTitle : '',
					idAccountTitleDebit : '',
					idAccountTitleCredit : '',
					flagDisabled : 0
				}
			],
		}
	*/
	_arrBtnTitleIns : [],
	addBtnTitle : function(obj)
	{
		this._resetBtnTitle();
		this._varsBtnTitleValue = obj;
		var eleUl = $(document.createElement('ul'));
		this._eleBtnTitleWrap.insert(eleUl);
		for (var i = 0; i < obj.arr.length; i++) {
			var eleLi = $(document.createElement('li'));
			eleUl.insert(eleLi);
			if (obj.arr[i].flagDisabled) {
				eleLi.insert(obj.arr[i].strTitle);
				continue;
			}
			var insBtn = new Code_Lib_Btn();
			insBtn.iniBtnTextTarget({
				eleInsert  : eleLi,
				id         : '',
				strFunc    : '_checkBtnTitle',
				strTitle   : obj.arr[i].strTitle,
				insCurrent : this,
				vars       : obj.arr[i]
			});
			this._setListener({ins : insBtn});
			this._arrBtnTitleIns.push(insBtn);
		}
	},

	/**
	 *
	*/
	getVarsBtnTitleValue : function(obj)
	{
		return this._varsBtnTitleValue;
	},

	/**
	 *
	*/
	_varsBtnTitleValue : {},
	_checkBtnTitle : function(obj)
	{
		var keyDebit = obj.vars.vars.idAccountTitleDebit;
		var keyCredit = obj.vars.vars.idAccountTitleCredit;
		var varsDebit = {
			vars          : {id : this.vars.varsDetail.varsDetail[0].id},
			flagKey       : 'idAccountTitle',
			flagDebit     : 1,
			value         : keyDebit
		};
		this._updateEditVars({
			arr  : this.vars.varsDetail.varsDetail,
			vars : varsDebit
		});
		var varsCredit = {
			vars          : {id : this.vars.varsDetail.varsDetail[0].id},
			flagKey       : 'idAccountTitle',
			flagDebit     : 0,
			value         : keyCredit
		};
		this._updateEditVars({
			arr  : this.vars.varsDetail.varsDetail,
			vars : varsCredit
		});
	},

	/**
	 *
	*/
	_iniBtnDictionary : function(obj)
	{
		this._setBtnDictionary();
	},

	/**
	 *
	*/
	_setBtnDictionary : function()
	{
		var insBtn = new Code_Lib_Btn();
		var id = this.idSelf + 'BtnDictionary';
		insBtn.iniBtn({
			eleInsert  : this._eleBtnWrap,
			id         : id,
			strFunc    : '_mousedownBtnDictionary',
			strTitle   : this.vars.varsTmpl.strBtnDictionary,
			insCurrent : this,
			flagATag   : null,
			path       : null,
			vars       : {}
		});
		this._setListener({ins : insBtn});
		var ele = $(id);
		ele.setStyle({
			marginBottom : '5px',
			marginRight  : '5px'
		});
	},

	/**
	 *
	*/
	_valueBtnDictionary : '',
	_mousedownBtnDictionary : function(obj)
	{
		var vars = {
			flagTag       : 'selectShortCut',
			flagInputType : '',
			numMaxlength  : 0,
			numWidth      : 0,
			unitWidth     : 'px',
			numHeight     : 0,
			unitHeight    : 'px',
			arrayOption   : this.vars.varsTmpl.varsSelectTag.varsBtnDictionary,
			value         : this._valueBtnDictionary,
			vars          : {}
		};

		this.insFormTemp = new Code_Lib_FormTemp({
			insRoot    : this.insRoot,
			eleInsert  : $(this.idSelf).up('.codeLibWindow', 0),
			insCurrent : this,
			idSelf     : this.idSelf + 'Edit',
			allot      : this._getBtnDictionaryEditAllot(),
			vars       : this._getEditVars({vars : vars, ele : $(this.idSelf + 'BtnDictionary')})
		});
	},

	/**
	 *
	 */
	_getBtnDictionaryEditAllot : function()
	{
		var allot = function(obj) {
			var insCurrent = obj.insCurrent;
			if (obj.from == 'removeWrap') {
				insCurrent.resetVarsDetail();
				var arr = obj.vars.value.split(',');
				insCurrent._valueBtnDictionary = obj.vars.value;
				var keyDebit = arr[0];
				var keyCredit = arr[1];
				var varsDebit = {
					vars          : {id : insCurrent.vars.varsDetail.varsDetail[0].id},
					flagKey       : 'idAccountTitle',
					flagDebit     : 1,
					value         : keyDebit
				};
				insCurrent._updateEditVars({
					arr  : insCurrent.vars.varsDetail.varsDetail,
					vars : varsDebit
				});
				var varsCredit = {
					vars          : {id : insCurrent.vars.varsDetail.varsDetail[0].id},
					flagKey       : 'idAccountTitle',
					flagDebit     : 0,
					value         : keyCredit
				};
				insCurrent._updateEditVars({
					arr  : insCurrent.vars.varsDetail.varsDetail,
					vars : varsCredit
				});
			}
		};

		return allot;
	},

	/**
	 *
	 */
	iniReload : function()
	{
		this.allot({
			insCurrent : this.insCurrent,
			from       : 'preIniReload'
		});
		this.stopListener();
		this.removeWrap();
		this._iniWrap();
		this._iniBtnInput();
		this._iniSummary();
		this._iniDetail();
		this.allot({
			insCurrent : this.insCurrent,
			from       : 'afterIniReload'
		});
	},

	/**
	 *
	 */
	_iniVarsLoad : function(obj)
	{
		this.vars = {};
		this.vars.varsStatus = {};
		this.vars.varsSummary = {};
		this.vars.varsRule = (Object.toJSON(obj.varsRule)).evalJSON();
		this.vars.varsDetail = {};
		this.vars.varsTmpl = {};
	},

	/**
	 *
	 */
	updateVarsRule : function(obj)
	{
		this.vars.varsRule = (Object.toJSON(obj.varsRule)).evalJSON();
	},

	/**
	 *
	 */
	_iniVars : function(obj)
	{
		this.insRoot = (obj.insRoot)? obj.insRoot : null;
		this.insCurrent = (obj.insCurrent)? obj.insCurrent : null;
		this.insSelf = this;
		this.idSelf = (obj.idSelf)? obj.idSelf : null;
		this.varsTarget = (obj.varsTarget)? obj.varsTarget : null;

		this.vars.varsStatus = (Object.toJSON(obj.vars.varsStatus)).evalJSON();
		this.vars.varsSummary = (Object.toJSON(obj.vars.varsSummary)).evalJSON();
		this.vars.varsDetail = (Object.toJSON(obj.vars.varsDetail)).evalJSON();
		this.vars.varsTmpl = (Object.toJSON(obj.vars.varsTmpl)).evalJSON();

		this.eleInsert = (obj.eleInsert)? obj.eleInsert : null;
		this.eleInsertBtnLeft = (obj.eleInsertBtnLeft)? obj.eleInsertBtnLeft : null;
		this.eleInsertBtnRight = (obj.eleInsertBtnRight)? obj.eleInsertBtnRight : null;
		this.idWindow = (obj.idWindow)? obj.idWindow : null;
		this._varsSatmp = {};

		this._updateVarsEntityNation();
		this._iniCake();
	},

	/**
	 *
	 */
	_updateVarsEntityNation : function()
	{
		var varsEntityNation = (Object.toJSON(this.vars.varsRule.varsEntityNation)).evalJSON();
		var flagJson = (Object.toJSON(this.vars.varsDetail.varsEntityNation));
		if (flagJson == '[]' || flagJson == '{}') {
			this.vars.varsDetail.varsEntityNation = {};
			this.vars.varsDetail.varsEntityNation.flagConsumptionTaxFree = varsEntityNation.flagConsumptionTaxFree;
			this.vars.varsDetail.varsEntityNation.flagConsumptionTaxGeneralRule = varsEntityNation.flagConsumptionTaxGeneralRule;
			this.vars.varsDetail.varsEntityNation.flagConsumptionTaxDeducted = varsEntityNation.flagConsumptionTaxDeducted;
			this.vars.varsDetail.varsEntityNation.flagConsumptionTaxIncluding = varsEntityNation.flagConsumptionTaxIncluding;
		}
		this.vars.varsDetail.varsEntityNation.flagConsumptionTaxCalc = varsEntityNation.flagConsumptionTaxCalc;
		this.vars.varsDetail.varsEntityNation.flagConsumptionTaxWithoutCalc = varsEntityNation.flagConsumptionTaxWithoutCalc;
		this.vars.varsDetail.varsEntityNation.flagConsumptionTaxBusinessType = varsEntityNation.flagConsumptionTaxBusinessType;
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
		var arrNew = [];
		var num = 0;
		var data = {};
		var dataCheck = {};

		for (var i = 0; i < this._staticNumRecent; i++) {
			var idTarget = obj.data[i];
			if (!idTarget) {
				continue;
			}
			if (!this.vars.varsRule.arrAccountTitle.arrStrTitle[idTarget]) {
				continue;
			}
			if (dataCheck[idTarget]) {
				continue;
			}
			dataCheck[idTarget] = 1;
			this._varsRecentIdAccountTitle.push(idTarget);
			if (num < 11) {
				data = {
					strTitle        : this.vars.varsRule.arrAccountTitle.arrStrTitle[idTarget].strTitleFS,
					value           : '=' + idTarget,
					flagNotShortCut : 1,
				};
				arrNew.push(data);
			}
			num++;
		}

		var arrayOption = [];
		var flag = 0;
		var arrOption = this.vars.varsRule.arrAccountTitle.arrSelectTag;
		for (var i = 0; i < arrOption.length; i++) {
			if (arrOption[i].strTitle == '-- prev list --') {
				flag = 1;
				continue;
			}
			if (arrOption[i].strTitle == '-------------') {
				flag = 0;
				continue;
			}
			if (flag) {
				continue;
			}
			arrayOption.push(arrOption[i]);
		}
		this.vars.varsRule.arrAccountTitle.arrSelectTag = arrayOption;
		if (arrNew.length > 0) {
			data = {
				strTitle     : '-------------',
				value        : '',
				flagDisabled : 1
			};
			this.vars.varsRule.arrAccountTitle.arrSelectTag.unshift(data);
			for (var i = 0; i < arrNew.length; i++) {
				this.vars.varsRule.arrAccountTitle.arrSelectTag.unshift(arrNew[i]);
			}
			data = {
				strTitle     : '-- prev list --',
				value        : '',
				flagDisabled : 1
			};
			this.vars.varsRule.arrAccountTitle.arrSelectTag.unshift(data);
		}
	},

	/**
	 *
	*/
	_staticNumRecent : 20,
	_setCakeVars : function(obj)
	{
		var arr = this._varsRecentIdAccountTitle;
		for (var i = 0; i < this._staticNumRecent; i++) {
			this._varsCake[i] = '';
			if (arr[i]) {
				this._varsCake[i] = arr[i];
			}
		}

	},

	_varsRecentIdAccountTitle : [],
	_setVarsRecentIdAccountTitle : function(obj)
	{
		var arr = this._varsRecentIdAccountTitle;
		var arrNew = [];
		for (var i = 0; i < this._staticNumRecent; i++) {
			if (arr[i] == obj.idAccountTitle) {
				continue;
			}
			arrNew.push(arr[i]);
		}
		arrNew.unshift(obj.idAccountTitle);
		this._varsRecentIdAccountTitle = arrNew;
		this.setCake();
	},

	/**
	 *
	 */
	_iniListener : function()
	{
		this._extListener();
	},

	/**
	 * Wrap
	 */
	_varsWrapWidth : 0,
	_staticWrap : {numBar : 17},
	_iniWrap : function()
	{
		this._varsWrapWidth = 0;
		this._extWrap();
		this.eleWrap.addClassName('codePluginAccountingLibWrap');
		this.eleWrap.setStyle({
			width  : this._getWrapWidth() + 'px'
		});
	},

	/**
	 *
	 */
	_getWrapWidth : function()
	{
		var array = this.eleInsert.style.width.split('px');
		var numWidthWrap = parseFloat(array[0]);

		var numWidth = numWidthWrap - this._staticWrap.numBar;
		if (!this._varsWrapWidth) {
			this._varsWrapWidth = numWidth;

			return numWidth;

		} else {
			return this._varsWrapWidth;
		}
	},



	/**
	 *
	 */
	_iniSummary : function(obj)
	{
		if(!this.vars.varsStatus.flagSummaryUse) {
			this._setSummaryWrapLine();
			return;
		}
		this._setSummaryWrap();
		this._setSummaryVars();
		this._setSummary();
	},



	/**
	 *
	 */
	_getSeparateLine : function(obj)
	{
		var ele = $(document.createElement('span'));
		if (obj.flagDouble) ele.addClassName('codePluginAccountingLibLineDouble');
		else ele.addClassName('codePluginAccountingLibLineSingle');
		ele.setStyle({
			width  : this._getWrapWidth() + 'px'
		});

		return ele;
	},

	/**
	 *
	 */
	_staticSeparate : {numHeight : 26, numPadding : 5, numSingle : 1, numDouble : 3},
	_getSeparateColumn : function(obj)
	{
		var ele = $(document.createElement('span'));
		if (obj.flagDouble) ele.addClassName('codePluginAccountingLibColumnDouble');
		else ele.addClassName('codePluginAccountingLibColumnSingle');
		ele.setStyle({
			height  : this._staticSeparate.numHeight + 'px'
		});

		return ele;
	},



	/**
	 *
	 */
	_mousedownBtn : function(evt, obj)
	{
		evt.stop();
		this._setEdit(obj);
	},

	/**
	 *
	 */
	_mouseoverBtn : function(obj)
	{
		obj.ele.addClassName('codePluginAccountingLibBtnOver');
	},

	/**
	 *
	 */
	_mouseoutBtn : function(obj)
	{
		obj.ele.removeClassName('codePluginAccountingLibBtnOver');
	},

	/**
	 *
	 */
	_setEdit : function(obj)
	{
		this.insFormTemp = new Code_Lib_FormTemp({
			insRoot    : this.insRoot,
			eleInsert  : $(this.idSelf).up('.codeLibWindow', 0),
			insCurrent : this,
			idSelf     : this.idSelf + 'Edit',
			allot      : this._getEditAllot(),
			vars       : this._getEditVars(obj)
		});
	},

	/**
	 *
	 */
	_getEditVars : function(obj)
	{
		var varsStyle = this.allot({
			insCurrent : this.insCurrent,
			from       : '_getEditVars'
		});

		var varsFormTemp = (Object.toJSON(this.vars.varsTmpl.varsFormTemp)).evalJSON();
		varsFormTemp.varsStatus.numTop = obj.ele.offsetTop - varsStyle.numTop;
		varsFormTemp.varsStatus.numLeft = obj.ele.offsetLeft - varsStyle.numLeft;

		varsFormTemp.varsDetail = obj.vars;
		varsFormTemp.varsDetail.numWidth = obj.ele.offsetWidth;
		varsFormTemp.varsDetail.numHeight = obj.ele.offsetHeight;

		return varsFormTemp;
	},

	/**
	 *
	 */
	_getEditAllot : function()
	{
		var allot = function(obj) {
			var insCurrent = obj.insCurrent;
			if (obj.from == 'removeWrap') {
				insCurrent._updateEditVars({
					arr  : insCurrent.vars.varsDetail.varsDetail,
					vars : obj.vars
				});
				insCurrent.allot({
					insCurrent : insCurrent.insCurrent,
					from       : 'removeWrap'
				});
			}
		};

		return allot;
	},

	/**
	 *
	 */
	_setSeparateColumn : function(obj)
	{
		var numAll = this._getWrapWidth() - this._staticSeparate.numPadding * 8 - this._staticSeparate.numSingle * 4 - this._staticSeparate.numDouble;
		var numWidth = Math.round(numAll / 4);
		var numWidthRest = numAll - numWidth * 3;

		if (obj.varsBtnText) {
			obj.varsBtnText.flagDebit = 1;
			obj.varsBtnText.flagCol = 'key';
			obj.varsBtnText.flagUse = obj.varsBtnText.flagDebitKey;
		}
		this._setSeparateBlock({
			ele        : obj.eleWrap.down('.codePluginAccountingLibLineBlockDebitKey', obj.numArr),
			numWidth   : numWidth,
			strTitle   : obj.strDebitKey,
			strClassBg : obj.strClassBg,
			varsBtnText: obj.varsBtnText,
			varsForm   : (obj.varsForm)? ((obj.varsForm.debitKey)? obj.varsForm.debitKey : null) : null
		});

		if (obj.varsBtnText) {
			obj.varsBtnText.flagDebit = 1;
			obj.varsBtnText.flagCol = 'value';
			obj.varsBtnText.flagUse = obj.varsBtnText.flagDebitValue;
		}
		this._setSeparateBlock({
			ele        : obj.eleWrap.down('.codePluginAccountingLibLineBlockDebitValue', obj.numArr),
			numWidth   : numWidth,
			strTitle   : obj.strDebitValue,
			strClassBg : obj.strClassBg,
			varsBtnText: obj.varsBtnText,
			varsForm   : (obj.varsForm)? ((obj.varsForm.debitValue)? obj.varsForm.debitValue : null) : null
		});

		if (obj.varsBtnText) {
			obj.varsBtnText.flagDebit = 0;
			obj.varsBtnText.flagCol = 'key';
			obj.varsBtnText.flagUse = obj.varsBtnText.flagCreditKey;
		}
		this._setSeparateBlock({
			ele        : obj.eleWrap.down('.codePluginAccountingLibLineBlockCreditKey', obj.numArr),
			numWidth   : numWidth,
			strTitle   : obj.strCreditKey,
			strClassBg : obj.strClassBg,
			varsBtnText: obj.varsBtnText,
			varsForm   : (obj.varsForm)? ((obj.varsForm.creditKey)? obj.varsForm.creditKey : null) : null
		});

		if (obj.varsBtnText) {
			obj.varsBtnText.flagDebit = 0;
			obj.varsBtnText.flagCol = 'value';
			obj.varsBtnText.flagUse = obj.varsBtnText.flagCreditValue;
		}
		this._setSeparateBlock({
			ele        : obj.eleWrap.down('.codePluginAccountingLibLineBlockCreditValue', obj.numArr),
			numWidth   : numWidthRest,
			strTitle   : obj.strCreditValue,
			strClassBg : obj.strClassBg,
			varsBtnText: obj.varsBtnText,
			varsForm   : (obj.varsForm)? ((obj.varsForm.creditValue)? obj.varsForm.creditValue : null) : null
		});
	},

	/**
	 *
	 */
	_getSeparateBlock : function(obj)
	{
		if (obj.strClassBg) {
			obj.ele.addClassName(obj.strClassBg);
			if (obj.strTitle != '') {
				obj.ele.setStyle({
					textAlign  : 'left'
				});
			}
		}
		if (obj.strTitle != '') {
			obj.ele.title = obj.strTitle;
			obj.ele.insert(obj.strTitle);
		}
		obj.ele.setStyle({
			width  : obj.numWidth + 'px'
		});

		return obj.ele;
	},

	/**
	 *
	 */
	_setSeparateBlock : function(obj)
	{
		var ele = this._getSeparateBlock(obj);
		ele.removeClassName('codeLibBaseCursorPointer');
		ele.removeClassName('codePluginAccountingLibBgActive');
		if (obj.varsForm) {
			ele.addClassName('codeLibBaseCursorPointer');
			ele.addClassName('codePluginAccountingLibBgActive');
			if (this.vars.varsStatus.flagEditUse) {
				this.insListener.set({
					bindAsEvent : 1, insCurrent : this, event : 'mousedown',
					strFunc : '_mousedownBtn', ele : ele, vars : { ele : ele, vars : obj.varsForm }
				});
				this.insListener.set({bindAsEvent : 0, insCurrent : this, event : 'mouseover',
					strFunc : '_mouseoverBtn', ele : ele, vars : { ele : ele, vars : obj.varsForm }
				});
				this.insListener.set({bindAsEvent : 0, insCurrent : this, event : 'mouseout',
					strFunc : '_mouseoutBtn', ele : ele, vars : { ele : ele, vars : obj.varsForm }
				});

			}
		} else {
			if (this.vars.varsStatus.flagBtnTextUse) {
				if (obj.varsBtnText) {
					if (obj.varsBtnText.flagUse) {
						if (!obj.strTitle) {
							return ele;
						}
						if (obj.varsBtnText.flagRow == 'subConsumptionTax' || obj.varsBtnText.flagRow == 'mainConsumptionTax') {
							if (!this.vars.varsStatus.flagBtnTextTaxUse) {
								return ele;
							}
						}
						var insBtn = new Code_Lib_Btn();
						ele.innerHTML = '';

						insBtn.iniBtnTextTarget({
							eleInsert  : ele,
							id         : '',
							strFunc    : '_checkTextBtn',
							strTitle   : obj.strTitle,
							insCurrent : this,
							vars       : { vars : (Object.toJSON(obj.varsBtnText)).evalJSON() }
						});
						this._setListener({ins : insBtn});
					}
				}
			}
		}

		return ele;
	},

	/**
	 *
	 */
	_checkTextBtn : function(obj)
	{
		if (obj.vars.vars.vars.flagRow == 'subConsumptionTax') {
			var strDebit = 'Debit';
			if (!obj.vars.vars.vars.flagDebit) {
				strDebit = 'Credit';
			}
			var flagConsumptionTaxRule = '';
			var flagGeneral = this._getFlagGeneral();
			var flagConsumptionTaxDeducted = parseFloat(this.vars.varsDetail.varsEntityNation.flagConsumptionTaxDeducted);
			if (flagGeneral) {
				if (flagConsumptionTaxDeducted) {
					flagConsumptionTaxRule = obj.vars.vars.vars['arr' + strDebit].flagConsumptionTaxGeneralRuleEach;
				} else {
					flagConsumptionTaxRule = obj.vars.vars.vars['arr' + strDebit].flagConsumptionTaxGeneralRuleProration;
				}

			} else {
				flagConsumptionTaxRule = obj.vars.vars.vars['arr' + strDebit].flagConsumptionTaxSimpleRule;
			}
			obj.vars.vars.vars['arr' + strDebit].flagConsumptionTaxRule = flagConsumptionTaxRule;
		}
		this.allot({
			insCurrent : this.insCurrent,
			from       : '_checkTextBtn',
			vars       : obj.vars.vars
		});
	},


	/**
	 *
	 */
	_setSummaryVars : function()
	{
		var insDisplayComma = new Code_Lib_DisplayComma();
		this._setSummaryValue({arr : this.vars.varsDetail.varsDetail});

		this.vars.varsSummary.numSumDebit = parseFloat(this.vars.varsDetail.numSumDebit);
		this.vars.varsSummary.strSumDebit = insDisplayComma.get({
			num : this.vars.varsSummary.numSumDebit
		});
		this.vars.varsSummary.numSumCredit = parseFloat(this.vars.varsDetail.numSumCredit);
		this.vars.varsSummary.strSumCredit = insDisplayComma.get({
			num : this.vars.varsSummary.numSumCredit
		});
	},

	/**
	 *
	 */
	_setSummaryValue : function(obj)
	{
		var numSumDebit = 0;
		var numSumCredit = 0;

		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].arrDebit.numValue != '') {
				var numValueDebit = parseFloat(obj.arr[i].arrDebit.numValue);

				if (this.vars.varsRule.arrAccountTitle.arrStrTitle[obj.arr[i].arrDebit.idAccountTitle]) {
					var cut = this.vars.varsRule.arrAccountTitle.arrStrTitle[obj.arr[i].arrDebit.idAccountTitle];
					var flagConsumptionTaxRule = this._getFlagConsumptionTaxRule({
						flagDebit : 1,
						vars      : obj.arr[i],
						cut       : cut
					});
					var flagConsumptionTaxWithoutCalc = (obj.arr[i].arrDebit.flagConsumptionTaxWithoutCalc == '')? 0 : parseFloat(obj.arr[i].arrDebit.flagConsumptionTaxWithoutCalc);

					if (obj.arr[i].arrDebit.numValue
						 && flagConsumptionTaxRule.match(/^tax/)
						 && obj.arr[i].arrDebit.idAccountTitle != 'suspenseReceiptOfConsumptionTaxes'
						 && obj.arr[i].arrDebit.idAccountTitle != 'suspensePaymentConsumptionTaxes'
						 && flagConsumptionTaxWithoutCalc == 2
						 && obj.arr[i].arrDebit.numValueConsumptionTax != ''
					) {
						numValueDebit += parseFloat(obj.arr[i].arrDebit.numValueConsumptionTax);
					}
				}
				numSumDebit += numValueDebit;
			}

			if (obj.arr[i].arrCredit.numValue != '') {
				var numValueCredit = parseFloat(obj.arr[i].arrCredit.numValue);
				if (this.vars.varsRule.arrAccountTitle.arrStrTitle[obj.arr[i].arrCredit.idAccountTitle]) {
					var cut = this.vars.varsRule.arrAccountTitle.arrStrTitle[obj.arr[i].arrCredit.idAccountTitle];
					var flagConsumptionTaxRule = this._getFlagConsumptionTaxRule({
						flagDebit : 0,
						vars      : obj.arr[i],
						cut       : cut
					});
					var flagConsumptionTaxWithoutCalc = (obj.arr[i].arrCredit.flagConsumptionTaxWithoutCalc == '')? 0 : parseFloat(obj.arr[i].arrCredit.flagConsumptionTaxWithoutCalc);

					if (obj.arr[i].arrCredit.numValue
						 && flagConsumptionTaxRule.match(/^tax/)
						 && obj.arr[i].arrCredit.idAccountTitle != 'suspenseReceiptOfConsumptionTaxes'
						 && obj.arr[i].arrCredit.idAccountTitle != 'suspensePaymentConsumptionTaxes'
						 && flagConsumptionTaxWithoutCalc == 2
						 && obj.arr[i].arrCredit.numValueConsumptionTax != ''
					) {
						numValueCredit += parseFloat(obj.arr[i].arrCredit.numValueConsumptionTax);
					}
				}
				numSumCredit += numValueCredit;
			}
		}

		this.vars.varsDetail.numSumDebit = numSumDebit;
		this.vars.varsDetail.numSumCredit = numSumCredit;

	},

	/**
<div id="#{idSelf}_eleWrapSummary" class="codePluginAccountingLibWrapSummary">
	<span class="codePluginAccountingLibLineSingle" style="width: #{numWrapWidth}px;"></span>
	<div id="#{idSelf}_eleBarWrap" class="codePluginAccountingLibSummaryBarWrap">
		<span class="codePluginAccountingLibColumnSingle" style="height: #{numHeight}px;"></span>
		<span id="#{idSelf}_eleBar" class="codePluginAccountingLibSummaryBar unselect" style="width: #{numBarWidth}px;">
			<span id="#{idSelf}_eleBarCommand" style="height: #{numBox}px;">
				<span id="#{idSelf}_eleBarCommand_Add" class="codePluginAccountingLibAdd codeLibBaseCursorPointer unselect" style="width: #{numBox}px; height: #{numBox}px;"></span>
				<span id="#{idSelf}_eleBarCommand_AddTitle" class="codePluginAccountingLibSubTitle">#{strAdd}</span>
				<span id="#{idSelf}_eleBarCommand_Remove" class="codePluginAccountingLibRemove codeLibBaseCursorPointer unselect" style="width: #{numBox}px; height: #{numBox}px;"></span>
				<span id="#{idSelf}_eleBarCommand_RemoveTitle" class="codePluginAccountingLibSubTitle">#{strRemove}</span>
				<span id="#{idSelf}_eleBarCommand_SortSide" class="codePluginAccountingLibSortSide codeLibBaseCursorPointer unselect" style="width: #{numBox}px; height: #{numBox}px;"></span>
				<span id="#{idSelf}_eleBarCommand_SortSideTitle" class="codePluginAccountingLibSubTitle">#{strSortSide}</span>
			</span>
			<span id="#{idSelf}_eleBarUnit" style="height: #{numBox}px;">#{strUnit}</span>
		</span>
		<span class="codePluginAccountingLibColumnSingle" style="height: #{numHeight}px;"></span>
	</div>
	<span class="codePluginAccountingLibLineSingle" style="width: #{numWrapWidth}px;"></span>
	<div id="#{idSelf}_eleLineSum" class="codePluginAccountingLibLine codePluginAccountingLibLineSum unselect" style="width: #{numWrapWidth}px;">
		<span class="codePluginAccountingLibColumnSingle" style="height: #{numHeight}px;"></span>
		<span class="codePluginAccountingLibLineBlock codePluginAccountingLibLineBlockDebitKey" title="" ></span>
		<span class="codePluginAccountingLibColumnSingle" style="height: #{numHeight}px;"></span>
		<span class="codePluginAccountingLibLineBlock codePluginAccountingLibLineBlockDebitValue" title="" ></span>
		<span class="codePluginAccountingLibColumnDouble" style="height: #{numHeight}px;"></span>
		<span class="codePluginAccountingLibLineBlock codePluginAccountingLibLineBlockCreditKey" title="" ></span>
		<span class="codePluginAccountingLibColumnSingle" style="height: #{numHeight}px;"></span>
		<span class="codePluginAccountingLibLineBlock codePluginAccountingLibLineBlockCreditValue" title="" ></span>
		<span class="codePluginAccountingLibColumnSingle" style="height: #{numHeight}px;"></span>
	</div>
	<span class="codePluginAccountingLibLineSingle" style="width: #{numWrapWidth}px;"></span>
</div>
	 */
	_strHtmlSummary : '<div id="#{idSelf}_eleWrapSummary" class="codePluginAccountingLibWrapSummary"><span class="codePluginAccountingLibLineSingle" style="width: #{numWrapWidth}px;"></span><div id="#{idSelf}_eleBarWrap" class="codePluginAccountingLibSummaryBarWrap"><span class="codePluginAccountingLibColumnSingle" style="height: #{numHeight}px;"></span><span id="#{idSelf}_eleBar" class="codePluginAccountingLibSummaryBar unselect" style="width: #{numBarWidth}px;"><span id="#{idSelf}_eleBarCommand" style="height: #{numBox}px;"><span id="#{idSelf}_eleBarCommand_Add" class="codePluginAccountingLibAdd codeLibBaseCursorPointer unselect" style="width: #{numBox}px; height: #{numBox}px;"></span><span id="#{idSelf}_eleBarCommand_AddTitle" class="codePluginAccountingLibSubTitle">#{strAdd}</span><span id="#{idSelf}_eleBarCommand_Remove" class="codePluginAccountingLibRemove codeLibBaseCursorPointer unselect" style="width: #{numBox}px; height: #{numBox}px;"></span><span id="#{idSelf}_eleBarCommand_RemoveTitle" class="codePluginAccountingLibSubTitle">#{strRemove}</span><span id="#{idSelf}_eleBarCommand_SortSide" class="codePluginAccountingLibSortSide codeLibBaseCursorPointer unselect" style="width: #{numBox}px; height: #{numBox}px;"></span><span id="#{idSelf}_eleBarCommand_SortSideTitle" class="codePluginAccountingLibSubTitle">#{strSortSide}</span></span><span id="#{idSelf}_eleBarUnit" style="height: #{numBox}px;">#{strUnit}</span></span><span class="codePluginAccountingLibColumnSingle" style="height: #{numHeight}px;"></span></div><span class="codePluginAccountingLibLineSingle" style="width: #{numWrapWidth}px;"></span><div id="#{idSelf}_eleLineSum" class="codePluginAccountingLibLine codePluginAccountingLibLineSum unselect" style="width: #{numWrapWidth}px;"><span class="codePluginAccountingLibColumnSingle" style="height: #{numHeight}px;"></span><span class="codePluginAccountingLibLineBlock codePluginAccountingLibLineBlockDebitKey" title="" style=""></span><span class="codePluginAccountingLibColumnSingle" style="height: #{numHeight}px;"></span><span class="codePluginAccountingLibLineBlock codePluginAccountingLibLineBlockDebitValue" title="" style=""></span><span class="codePluginAccountingLibColumnDouble" style="height: #{numHeight}px;"></span><span class="codePluginAccountingLibLineBlock codePluginAccountingLibLineBlockCreditKey" title="" style=""></span><span class="codePluginAccountingLibColumnSingle" style="height: #{numHeight}px;"></span><span class="codePluginAccountingLibLineBlock codePluginAccountingLibLineBlockCreditValue" title="" style=""></span><span class="codePluginAccountingLibColumnSingle" style="height: #{numHeight}px;"></span></div><span class="codePluginAccountingLibLineSingle" style="width: #{numWrapWidth}px;"></span></div>',
	eleWrapSummary : null,
	_setSummaryWrap : function()
	{
		var numBarWidth = this._getWrapWidth() - this._staticSummary.numPadding * 2 - this._staticSummary.numSingle * 2;
		var data = this._strHtmlSummary.interpolate({
			idSelf       : this.idSelf,
			numWrapWidth : this._getWrapWidth(),
			numBox       : this._staticSummary.numBox,
			numHeight    : this._staticSeparate.numHeight,
			numBarWidth  : numBarWidth,
			strAdd       : this.varsLoad.varsSummary.strAdd,
			strRemove    : this.varsLoad.varsSummary.strRemove,
			strSortSide  : this.varsLoad.varsSummary.strSortSide,
			strUnit      : this.vars.varsTmpl.strUnit
		});

		this.eleWrap.insert(data);
		this.eleWrapSummary = $(this.idSelf + '_eleWrapSummary');
	},

	_strHtmlSummaryLine : '<div id="#{idSelf}_eleWrapSummary" class="codePluginAccountingLibWrapSummary"><span class="codePluginAccountingLibLineSingle" style="width: #{numWrapWidth}px;"></span></div>',
	_setSummaryWrapLine : function()
	{
		var data = this._strHtmlSummaryLine.interpolate({
			idSelf       : this.idSelf,
			numWrapWidth : this._getWrapWidth(),
		});

		this.eleWrap.insert(data);
		this.eleWrapSummary = $(this.idSelf + '_eleWrapSummary');
	},

	/**
	 *
	 */
	_staticSummary : {numHeight : 26, numIdle : 5, numPadding : 5, numDouble : 3, numSingle : 1, numBox : 16},
	eleBarWrap : null,
	_setSummary : function()
	{
		this.eleBarWrap = $(this.idSelf + '_eleBarWrap');
		var eleBar = $(this.idSelf + '_eleBar');
		var eleBarCommand = $(this.idSelf + '_eleBarCommand');

		var arr = ['Add', 'Remove', 'SortSide'];
		for (var i = 0; i < arr.length; i++) {
			var ele = $(this.idSelf + '_eleBarCommand_' + arr[i]);
			var eleTitle = $(this.idSelf + '_eleBarCommand_' + arr[i] + 'Title');
			var flag = 0;
			if (this.vars.varsStatus.flagEditUse) {
				if (this.vars.varsStatus['flag'+ arr[i] +'Use']) {
					flag = 1;
					this.insListener.set({
						bindAsEvent : 1, insCurrent : this, event : 'mousedown',
						strFunc : '_mousedownBar' + arr[i], ele : ele, vars : ''
					});
				}
			}
			if (!flag) {
				ele.hide();
				eleTitle.hide();
			}
		}

		var eleBarUnit = $(this.idSelf + '_eleBarUnit');
		$(this.insRoot.vars.varsSystem.id.root).insert(eleBarUnit);
		var numWidthUnit = eleBarUnit.offsetWidth + this._staticSummary.numIdle;
		eleBar.insert(eleBarUnit);

		var numBarWidth = this._getWrapWidth() - this._staticSummary.numPadding * 2 - this._staticSummary.numSingle * 2;
		var numWidthCommand = numBarWidth - numWidthUnit;

		eleBarUnit.setStyle({
			width  : numWidthUnit + 'px'
		});

		eleBarCommand.setStyle({
			width  : numWidthCommand + 'px'
		});

		var eleLineSum = $(this.idSelf + '_eleLineSum');

		var varsBtnText = {
			flagRow         : 'summary',
			flagDebitKey    : 0,
			flagDebitValue  : 1,
			flagCreditKey   : 0,
			flagCreditValue : 1,
			varsSummary     : this.vars.varsSummary
		};

		this._setSeparateColumn({
			numArr         : 0,
			eleWrap        : eleLineSum,
			varsBtnText    : varsBtnText,
			strDebitKey    : this.vars.varsTmpl.strDebitSum,
			strDebitValue  : this.vars.varsSummary.strSumDebit,
			strCreditKey   : this.vars.varsTmpl.strCreditSum,
			strCreditValue : this.vars.varsSummary.strSumCredit
		});
	},

	/**
	 *
	 */
	_mousedownBarAdd : function(obj)
	{
		this._updateBarAdd();
		this.iniReload();
	},

	/**
	 *
	 */
	_updateBarAdd : function()
	{
		var data = (Object.toJSON(this.vars.varsTmpl.varsDetailVarsDetail)).evalJSON();
		data.id = new Date().getTime();
		this.vars.varsDetail.varsDetail.push(data);
	},

	/**
	 *
	 */
	_mousedownBarSortSide : function(obj)
	{
		this.vars.varsDetail.varsDetail = this._updateBarSortSide({
			arr  : this.vars.varsDetail.varsDetail
		});
		this.iniReload();
	},

	/**
	 *
	 */
	_updateBarSortSide : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			var dataDebit = (Object.toJSON(obj.arr[i].arrDebit)).evalJSON();
			var dataCredit = (Object.toJSON(obj.arr[i].arrCredit)).evalJSON();
			obj.arr[i].arrDebit = dataCredit;
			obj.arr[i].arrCredit = dataDebit;
		}

		return obj.arr;
	},

	/**
	 *
	 */
	_mousedownBarRemove : function(evt)
	{
		if (evt) evt.stop();
		this.vars.varsDetail.varsDetail = [];
		this._updateBarAdd();
		this.iniReload();
	},

	/**
	 *
	 */
	resetVarsDetail : function()
	{
		this.vars.varsDetail.varsDetail = [];
		this._updateBarAdd();
		this.iniReload();
	},

	/**

<div id="#{id}" class="codePluginAccountingLibDetailWrap">
	<div id="#{id}_eleMainWrap" class="codePluginAccountingLibDetailMainWrap">
		<div id="#{id}_eleBarWrap" class="codePluginAccountingLibDetailBarWrap">
			<span class="codePluginAccountingLibColumnSingle" style="height: #{numHeight}px;"></span>
			<span id="#{id}_eleBar" class="codePluginAccountingLibDetailBar unselect" >
				<span id="#{id}_eleBarCommand" style="height: #{numBox}px;">
					<span id="#{id}_eleBarCommand_Copy" class="codePluginAccountingLibCopy codeLibBaseCursorPointer unselect" style="width: #{numBox}px; height: #{numBox}px;"></span>
					<span id="#{id}_eleBarCommand_CopyTitle" class="codePluginAccountingLibSubTitle">#{strCopy}</span>
					<span id="#{id}_eleBarCommand_Remove" class="codePluginAccountingLibRemove codeLibBaseCursorPointer unselect" style="width: #{numBox}px; height: #{numBox}px;"></span>
					<span id="#{id}_eleBarCommand_RemoveTitle" class="codePluginAccountingLibSubTitle">#{strRemove}</span>
					<span id="#{id}_eleBarCommand_SortSide" class="codePluginAccountingLibSortSide codeLibBaseCursorPointer unselect" style="width: #{numBox}px; height: #{numBox}px;"></span>
					<span id="#{id}_eleBarCommand_SortSideTitle" class="codePluginAccountingLibSubTitle">#{strSortSide}</span>
					<span id="#{id}_eleBarCommand_SortUp" class="codePluginAccountingLibSortUp codeLibBaseCursorPointer unselect" style="width: #{numBox}px; height: #{numBox}px;"></span>
					<span id="#{id}_eleBarCommand_SortUpTitle" class="codePluginAccountingLibSubTitle">#{strSortUp}</span>
					<span id="#{id}_eleBarCommand_SortDown" class="codePluginAccountingLibSortDown codeLibBaseCursorPointer unselect" style="width: #{numBox}px; height: #{numBox}px;"></span>
					<span id="#{id}_eleBarCommand_SortDownTitle" class="codePluginAccountingLibSubTitle">#{strSortDown}</span>
				</span>
			</span>
			<span class="codePluginAccountingLibColumnSingle" style="height: #{numHeight}px;"></span>
		</div>
		<span class="codePluginAccountingLibLineSingle" style="width: #{numWrapWidth}px;"></span>
		<span class="codePluginAccountingLibColumnSingle" style="height: #{numHeight}px;"></span>
		<span class="codePluginAccountingLibLineBlock codePluginAccountingLibLineBlockDebitKey" title="" ></span>
		<span class="codePluginAccountingLibColumnSingle" style="height: #{numHeight}px;"></span>
		<span class="codePluginAccountingLibLineBlock codePluginAccountingLibLineBlockDebitValue" title="" ></span>
		<span class="codePluginAccountingLibColumnDouble" style="height: #{numHeight}px;"></span>
		<span class="codePluginAccountingLibLineBlock codePluginAccountingLibLineBlockCreditKey" title="" ></span>
		<span class="codePluginAccountingLibColumnSingle" style="height: #{numHeight}px;"></span>
		<span class="codePluginAccountingLibLineBlock codePluginAccountingLibLineBlockCreditValue" title="" ></span>
		<span class="codePluginAccountingLibColumnSingle" style="height: #{numHeight}px;"></span>
		<span class="codePluginAccountingLibLineSingle" style="width: #{numWrapWidth}px;"></span>
		<span class="codePluginAccountingLibColumnSingle" style="height: #{numHeight}px;"></span>
		<span class="codePluginAccountingLibLineBlock codePluginAccountingLibLineBlockDebitKey" ></span>
		<span class="codePluginAccountingLibColumnSingle" style="height: #{numHeight}px;"></span>
		<span class="codePluginAccountingLibLineBlock codePluginAccountingLibLineBlockDebitValue" ></span>
		<span class="codePluginAccountingLibColumnDouble" style="height: #{numHeight}px;"></span>
		<span class="codePluginAccountingLibLineBlock codePluginAccountingLibLineBlockCreditKey" ></span>
		<span class="codePluginAccountingLibColumnSingle" style="height: #{numHeight}px;"></span>
		<span class="codePluginAccountingLibLineBlock codePluginAccountingLibLineBlockCreditValue" ></span>
		<span class="codePluginAccountingLibColumnSingle" style="height: #{numHeight}px;"></span>
	</div>
	<div id="#{id}_eleSubWrap" class="codePluginAccountingLibDetailSubWrap">
		<span class="codePluginAccountingLibLineSingle" style="width: #{numWrapWidth}px;"></span>
		<span class="codePluginAccountingLibColumnSingle" style="height: #{numHeight}px;"></span>
		<span class="codePluginAccountingLibLineBlock codeLibBaseBgNoactive codePluginAccountingLibLineBlockDebitKey" ></span>
		<span class="codePluginAccountingLibColumnSingle" style="height: #{numHeight}px;"></span>
		<span class="codePluginAccountingLibLineBlock codeLibBaseBgNoactive codePluginAccountingLibLineBlockDebitValue" ></span>
		<span class="codePluginAccountingLibColumnDouble" style="height: #{numHeight}px;"></span>
		<span class="codePluginAccountingLibLineBlock codeLibBaseBgNoactive codePluginAccountingLibLineBlockCreditKey" ></span>
		<span class="codePluginAccountingLibColumnSingle" style="height: #{numHeight}px;"></span>
		<span class="codePluginAccountingLibLineBlock codeLibBaseBgNoactive codePluginAccountingLibLineBlockCreditValue" ></span>
		<span class="codePluginAccountingLibColumnSingle" style="height: #{numHeight}px;"></span>
		<span class="codePluginAccountingLibLineSingle" style="width: #{numWrapWidth}px;"></span>
		<span class="codePluginAccountingLibColumnSingle" style="height: #{numHeight}px;"></span>
		<span class="codePluginAccountingLibLineBlock codeLibBaseBgNoactive codePluginAccountingLibLineBlockDebitKey" ></span>
		<span class="codePluginAccountingLibColumnSingle" style="height: #{numHeight}px;"></span>
		<span class="codePluginAccountingLibLineBlock codeLibBaseBgNoactive codePluginAccountingLibLineBlockDebitValue" ></span>
		<span class="codePluginAccountingLibColumnDouble" style="height: #{numHeight}px;"></span>
		<span class="codePluginAccountingLibLineBlock codeLibBaseBgNoactive codePluginAccountingLibLineBlockCreditKey" ></span>
		<span class="codePluginAccountingLibColumnSingle" style="height: #{numHeight}px;"></span>
		<span class="codePluginAccountingLibLineBlock codeLibBaseBgNoactive codePluginAccountingLibLineBlockCreditValue" ></span>
		<span class="codePluginAccountingLibColumnSingle" style="height: #{numHeight}px;"></span>
	</div>
</div>
<span class="codePluginAccountingLibLineSingle" style="width: #{numWrapWidth}px;"></span>
	 */
	_strHtmlDetail : '<div id="#{id}" class="codePluginAccountingLibDetailWrap"><div id="#{id}_eleMainWrap" class="codePluginAccountingLibDetailMainWrap"><div id="#{id}_eleBarWrap" class="codePluginAccountingLibDetailBarWrap"><span class="codePluginAccountingLibColumnSingle" style="height: #{numHeight}px;"></span><span id="#{id}_eleBar" class="codePluginAccountingLibDetailBar unselect" style=""><span id="#{id}_eleBarCommand" style="height: #{numBox}px;"><span id="#{id}_eleBarCommand_Copy" class="codePluginAccountingLibCopy codeLibBaseCursorPointer unselect" style="width: #{numBox}px; height: #{numBox}px;"></span><span id="#{id}_eleBarCommand_CopyTitle" class="codePluginAccountingLibSubTitle">#{strCopy}</span><span id="#{id}_eleBarCommand_Remove" class="codePluginAccountingLibRemove codeLibBaseCursorPointer unselect" style="width: #{numBox}px; height: #{numBox}px;"></span><span id="#{id}_eleBarCommand_RemoveTitle" class="codePluginAccountingLibSubTitle">#{strRemove}</span><span id="#{id}_eleBarCommand_SortSide" class="codePluginAccountingLibSortSide codeLibBaseCursorPointer unselect" style="width: #{numBox}px; height: #{numBox}px;"></span><span id="#{id}_eleBarCommand_SortSideTitle" class="codePluginAccountingLibSubTitle">#{strSortSide}</span><span id="#{id}_eleBarCommand_SortUp" class="codePluginAccountingLibSortUp codeLibBaseCursorPointer unselect" style="width: #{numBox}px; height: #{numBox}px;"></span><span id="#{id}_eleBarCommand_SortUpTitle" class="codePluginAccountingLibSubTitle">#{strSortUp}</span><span id="#{id}_eleBarCommand_SortDown" class="codePluginAccountingLibSortDown codeLibBaseCursorPointer unselect" style="width: #{numBox}px; height: #{numBox}px;"></span><span id="#{id}_eleBarCommand_SortDownTitle" class="codePluginAccountingLibSubTitle">#{strSortDown}</span></span></span><span class="codePluginAccountingLibColumnSingle" style="height: #{numHeight}px;"></span></div><span class="codePluginAccountingLibLineSingle" style="width: #{numWrapWidth}px;"></span><span class="codePluginAccountingLibColumnSingle" style="height: #{numHeight}px;"></span><span class="codePluginAccountingLibLineBlock codePluginAccountingLibLineBlockDebitKey" title="" style=""></span><span class="codePluginAccountingLibColumnSingle" style="height: #{numHeight}px;"></span><span class="codePluginAccountingLibLineBlock codePluginAccountingLibLineBlockDebitValue" title="" style=""></span><span class="codePluginAccountingLibColumnDouble" style="height: #{numHeight}px;"></span><span class="codePluginAccountingLibLineBlock codePluginAccountingLibLineBlockCreditKey" title="" style=""></span><span class="codePluginAccountingLibColumnSingle" style="height: #{numHeight}px;"></span><span class="codePluginAccountingLibLineBlock codePluginAccountingLibLineBlockCreditValue" title="" style=""></span><span class="codePluginAccountingLibColumnSingle" style="height: #{numHeight}px;"></span><span class="codePluginAccountingLibLineSingle" style="width: #{numWrapWidth}px;"></span><span class="codePluginAccountingLibColumnSingle" style="height: #{numHeight}px;"></span><span class="codePluginAccountingLibLineBlock codePluginAccountingLibLineBlockDebitKey" style=""></span><span class="codePluginAccountingLibColumnSingle" style="height: #{numHeight}px;"></span><span class="codePluginAccountingLibLineBlock codePluginAccountingLibLineBlockDebitValue" style=""></span><span class="codePluginAccountingLibColumnDouble" style="height: #{numHeight}px;"></span><span class="codePluginAccountingLibLineBlock codePluginAccountingLibLineBlockCreditKey" style=""></span><span class="codePluginAccountingLibColumnSingle" style="height: #{numHeight}px;"></span><span class="codePluginAccountingLibLineBlock codePluginAccountingLibLineBlockCreditValue" style=""></span><span class="codePluginAccountingLibColumnSingle" style="height: #{numHeight}px;"></span></div><div id="#{id}_eleSubWrap" class="codePluginAccountingLibDetailSubWrap"><span class="codePluginAccountingLibLineSingle" style="width: #{numWrapWidth}px;"></span><span class="codePluginAccountingLibColumnSingle" style="height: #{numHeight}px;"></span><span class="codePluginAccountingLibLineBlock codeLibBaseBgNoactive codePluginAccountingLibLineBlockDebitKey" style=""></span><span class="codePluginAccountingLibColumnSingle" style="height: #{numHeight}px;"></span><span class="codePluginAccountingLibLineBlock codeLibBaseBgNoactive codePluginAccountingLibLineBlockDebitValue" style=""></span><span class="codePluginAccountingLibColumnDouble" style="height: #{numHeight}px;"></span><span class="codePluginAccountingLibLineBlock codeLibBaseBgNoactive codePluginAccountingLibLineBlockCreditKey" style=""></span><span class="codePluginAccountingLibColumnSingle" style="height: #{numHeight}px;"></span><span class="codePluginAccountingLibLineBlock codeLibBaseBgNoactive codePluginAccountingLibLineBlockCreditValue" style=""></span><span class="codePluginAccountingLibColumnSingle" style="height: #{numHeight}px;"></span><span class="codePluginAccountingLibLineSingle" style="width: #{numWrapWidth}px;"></span><span class="codePluginAccountingLibColumnSingle" style="height: #{numHeight}px;"></span><span class="codePluginAccountingLibLineBlock codeLibBaseBgNoactive codePluginAccountingLibLineBlockDebitKey" style=""></span><span class="codePluginAccountingLibColumnSingle" style="height: #{numHeight}px;"></span><span class="codePluginAccountingLibLineBlock codeLibBaseBgNoactive codePluginAccountingLibLineBlockDebitValue" style=""></span><span class="codePluginAccountingLibColumnDouble" style="height: #{numHeight}px;"></span><span class="codePluginAccountingLibLineBlock codeLibBaseBgNoactive codePluginAccountingLibLineBlockCreditKey" style=""></span><span class="codePluginAccountingLibColumnSingle" style="height: #{numHeight}px;"></span><span class="codePluginAccountingLibLineBlock codeLibBaseBgNoactive codePluginAccountingLibLineBlockCreditValue" style=""></span><span class="codePluginAccountingLibColumnSingle" style="height: #{numHeight}px;"></span></div></div><span class="codePluginAccountingLibLineSingle" style="width: #{numWrapWidth}px;"></span>',
	_iniDetail : function()
	{
		this._setDetailWrap();
		this._setDetail({arr : this.vars.varsDetail.varsDetail});

	},

	/**
	 *
	 */
	_setDetail : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			obj.arr[i].id = i;
			var id = this.idSelf + 'Line' + obj.arr[i].id;
			var data = this._strHtmlDetail.interpolate({
				id           : id,
				numWrapWidth : this._getWrapWidth(),
				numHeight    : this._staticSeparate.numHeight,
				strCopy      : this.varsLoad.varsLine.strCopy,
				strRemove    : this.varsLoad.varsLine.strRemove,
				strSortSide  : this.varsLoad.varsLine.strSortSide,
				strSortUp    : this.varsLoad.varsLine.strSortUp,
				strSortDown  : this.varsLoad.varsLine.strSortDown,
				numBox       : this._staticSummary.numBox
			});
			this.eleWrapDetail.insert(data);

			var eleWrap = $(id);

			var eleMainWrap = $(id + '_eleMainWrap');
			var eleSubWrap = $(id + '_eleSubWrap');

			this._setDetailBar({
				numArr      : 0,
				eleMainWrap : eleMainWrap,
				eleSubWrap  : eleSubWrap,
				vars        : obj.arr[i]
			});

			this._setDetailMainAccount({
				numArr      : 0,
				eleMainWrap : eleMainWrap,
				eleSubWrap  : eleSubWrap,
				vars        : obj.arr[i]
			});

			this._setDetailMainConsumptionTax({
				numArr      : 1,
				eleMainWrap : eleMainWrap,
				eleSubWrap  : eleSubWrap,
				vars        : obj.arr[i]
			});

			this._setDetailSubConsumptionTax({
				numArr      : 0,
				eleMainWrap : eleMainWrap,
				eleSubWrap  : eleSubWrap,
				vars        : obj.arr[i]
			});

			this._setDetailSubSystem({
				numArr      : 1,
				eleMainWrap : eleMainWrap,
				eleSubWrap  : eleSubWrap,
				vars        : obj.arr[i]
			});
		}
	},

	/**
	 *
	 */
	_setDetailBar : function(obj)
	{
		var id = this.idSelf + 'Line' + obj.vars.id;
		var eleBarWrap = $(id + '_eleBarWrap');
		var eleBar = $(id + '_eleBar');

		var numBarWidth = this._getWrapWidth() - this._staticSummary.numPadding * 2 - this._staticSummary.numSingle * 2;
		eleBar.setStyle({
			width  : numBarWidth + 'px',
		});

		var eleBarCommand = $(id + '_eleBarCommand');
		eleBarCommand.setStyle({
			width  : numBarWidth + 'px'
		});

		var arr = ['Copy', 'Remove', 'SortSide', 'SortUp', 'SortDown'];
		for (var i = 0; i < arr.length; i++) {
			var ele = $(id + '_eleBarCommand_' + arr[i]);
			var eleTitle = $(id + '_eleBarCommand_' + arr[i] + 'Title');
			var flag = 0;
			if (this.vars.varsStatus.flagEditUse) {
				if (this.vars.varsStatus['flag' + arr[i] + 'Use']) {
					flag = 1;
					this.insListener.set({
						bindAsEvent : 1, insCurrent : this, event : 'mousedown',
						strFunc : '_mousedown' + arr[i], ele : ele, vars : { vars : obj.vars }
					});
				}
			}
			if (!flag) {
				ele.hide();
				eleTitle.hide();
			}
		}

	},

	/**
	 *
	 */
	_setDetailSubSystem : function(obj)
	{
		var insDisplayComma = new Code_Lib_DisplayComma();
		var strDebitKey = '';
		var strDebitValue = '';
		var strCreditKey = '';
		var strCreditValue = '';
		var varsForm = {};
		var debitKey = null;
		var debitValue = null;
		var creditKey = null;
		var creditValue = null;

		if (this.vars.varsRule.arrAccountTitle.arrStrTitle[obj.vars.arrDebit.idAccountTitle]) {
			var idAccountTitle = obj.vars.arrDebit.idAccountTitle;
			var idSubAccountTitle = obj.vars.arrDebit.idSubAccountTitle;

			if (this.vars.varsRule.arrSubAccountTitle.arrStrTitle[idAccountTitle]) {
				if (this.vars.varsRule.arrSubAccountTitle.arrStrTitle[idAccountTitle][idSubAccountTitle]) {
					strDebitKey = this.vars.varsRule.arrSubAccountTitle.arrStrTitle[idAccountTitle][idSubAccountTitle].strTitle;
				}
				if (this.vars.varsStatus.flagEditUse) {
					var varsBlank = (Object.toJSON(this.vars.varsTmpl.varsBlank)).evalJSON();
					var arrayOption = (Object.toJSON(this.vars.varsRule.arrSubAccountTitle.arrSelectTag[idAccountTitle])).evalJSON();
					arrayOption.unshift(varsBlank);
					debitKey = {
						flagTag       : 'selectShortCut',
						flagInputType : '',
						numMaxlength  : 0,
						numWidth      : 0,
						unitWidth     : 'px',
						numHeight     : 0,
						unitHeight    : 'px',
						arrayOption   : arrayOption,
						flagKey       : 'idSubAccountTitle',
						flagDebit     : 1,
						value         : idSubAccountTitle,
						vars          : obj.vars
					};

				}
				varsForm.debitKey = debitKey;
			}

			var idDepartment = obj.vars.arrDebit.idDepartment;
			if (this.vars.varsRule.arrDepartment.arrSelectTag.length > 1) {
				if (this.vars.varsRule.arrDepartment.arrStrTitle[idDepartment]) {
					strDebitValue = this.vars.varsRule.arrDepartment.arrStrTitle[idDepartment].strTitle;
				}
				if (this.vars.varsStatus.flagEditUse) {
					debitValue = {
						flagTag       : 'selectShortCut',
						flagInputType : '',
						numMaxlength  : 0,
						numWidth      : 0,
						unitWidth     : 'px',
						numHeight     : 0,
						unitHeight    : 'px',
						arrayOption   : this.vars.varsRule.arrDepartment.arrSelectTag,
						flagKey       : 'idDepartment',
						flagDebit     : 1,
						value         : idDepartment,
						vars          : obj.vars
					};
				}
				varsForm.debitValue = debitValue;
			}
		}

		if (this.vars.varsRule.arrAccountTitle.arrStrTitle[obj.vars.arrCredit.idAccountTitle]) {
			var idAccountTitle = obj.vars.arrCredit.idAccountTitle;
			var idSubAccountTitle = obj.vars.arrCredit.idSubAccountTitle;

			if (this.vars.varsRule.arrSubAccountTitle.arrStrTitle[idAccountTitle]) {
				if (this.vars.varsRule.arrSubAccountTitle.arrStrTitle[idAccountTitle][idSubAccountTitle]) {
					strCreditKey = this.vars.varsRule.arrSubAccountTitle.arrStrTitle[idAccountTitle][idSubAccountTitle].strTitle;
				}
				if (this.vars.varsStatus.flagEditUse) {
					var varsBlank = (Object.toJSON(this.vars.varsTmpl.varsBlank)).evalJSON();
					var arrayOption = (Object.toJSON(this.vars.varsRule.arrSubAccountTitle.arrSelectTag[idAccountTitle])).evalJSON();
					arrayOption.unshift(varsBlank);
					creditKey = {
						flagTag       : 'selectShortCut',
						flagInputType : '',
						numMaxlength  : 0,
						numWidth      : 0,
						unitWidth     : 'px',
						numHeight     : 0,
						unitHeight    : 'px',
						arrayOption   : arrayOption,
						flagKey       : 'idSubAccountTitle',
						flagDebit     : 0,
						value         : idSubAccountTitle,
						vars          : obj.vars
					};

				}
				varsForm.creditKey = creditKey;
			}

			var idDepartment = obj.vars.arrCredit.idDepartment;
			if (this.vars.varsRule.arrDepartment.arrSelectTag.length > 1) {
				if (this.vars.varsRule.arrDepartment.arrStrTitle[idDepartment]) {
					strCreditValue = this.vars.varsRule.arrDepartment.arrStrTitle[idDepartment].strTitle;
				}
				if (this.vars.varsStatus.flagEditUse) {
					creditValue = {
						flagTag       : 'selectShortCut',
						flagInputType : '',
						numMaxlength  : 0,
						numWidth      : 0,
						unitWidth     : 'px',
						numHeight     : 0,
						unitHeight    : 'px',
						arrayOption   : this.vars.varsRule.arrDepartment.arrSelectTag,
						flagKey       : 'idDepartment',
						flagDebit     : 0,
						value         : idDepartment,
						vars          : obj.vars
					};
				}
				varsForm.creditValue = creditValue;
			}

		}

		var varsBtnText = {
			flagRow         : 'subSystem',
			flagDebitKey    : 1,
			flagDebitValue  : 1,
			flagCreditKey   : 1,
			flagCreditValue : 1,
			arrDebit  : obj.vars.arrDebit,
			arrCredit : obj.vars.arrCredit
		};

		this._setSeparateColumn({
			numArr         : obj.numArr,
			eleWrap        : obj.eleSubWrap,
			varsBtnText    : varsBtnText,
			strDebitKey    : strDebitKey,
			strDebitValue  : strDebitValue,
			strCreditKey   : strCreditKey,
			strCreditValue : strCreditValue,
			strClassBg     : 'codeLibBaseBgNoactive',
			varsForm       : varsForm
		});

	},

	/**
	 *
	 */
	_getFlagConsumptionTaxRule : function(obj)
	{
		var str = 'arrCredit';
		if (obj.flagDebit) str = 'arrDebit';

		var flagGeneral = this._getFlagGeneral();
		var flagConsumptionTaxDeducted = parseFloat(this.vars.varsDetail.varsEntityNation.flagConsumptionTaxDeducted);

		if (flagGeneral) {
			if (flagConsumptionTaxDeducted) {
				flagConsumptionTaxRule = obj.vars[str].flagConsumptionTaxGeneralRuleEach;
			} else {
				flagConsumptionTaxRule = obj.vars[str].flagConsumptionTaxGeneralRuleProration;
			}

		} else {
			flagConsumptionTaxRule = obj.vars[str].flagConsumptionTaxSimpleRule;

		}
		if (!flagConsumptionTaxRule) {
			flagConsumptionTaxRule = 'none';
		}

		return flagConsumptionTaxRule;
	},


	/**
	 *
	 */
	_getFlagGeneral : function(obj)
	{
		var flagGeneral = parseFloat(this.vars.varsDetail.varsEntityNation.flagConsumptionTaxGeneralRule);

		return flagGeneral;
	},

	/**
	 *
	 */
	_getStrFlagConsumptionTaxWithoutCalc : function(obj)
	{
		var str = 'arrCredit';
		if (obj.flagDebit) str = 'arrDebit';

		var flagConsumptionTaxRule = this._getFlagConsumptionTaxRule({
			flagDebit   : obj.flagDebit,
			vars        : obj.vars,
			cut         : obj.cut
		});

		if (flagConsumptionTaxRule.match(/^tax/)
			  && obj.vars[str].idAccountTitle != 'suspenseReceiptOfConsumptionTaxes'
			  && obj.vars[str].idAccountTitle != 'suspensePaymentConsumptionTaxes'
		) {
			if (obj.vars[str].flagConsumptionTaxIncluding) {
				return '';

			} else {
				var flagConsumptionTaxWithoutCalc = '';
				if (obj.vars[str].flagConsumptionTaxWithoutCalc) {
					flagConsumptionTaxWithoutCalc = obj.vars[str].flagConsumptionTaxWithoutCalc;

				} else {
					flagConsumptionTaxWithoutCalc = parseFloat(this.vars.varsDetail.varsEntityNation.flagConsumptionTaxWithoutCalc);
				}
				return this.vars.varsTmpl.varsStrTitle.flagConsumptionTaxWithoutCalc[flagConsumptionTaxWithoutCalc];
			}

		}

		return '';
	},

	/**
	 *
	 */
	_setDetailSubConsumptionTax : function(obj)
	{
		var insDisplayComma = new Code_Lib_DisplayComma();
		var strDebitKey = '';
		var strDebitValue = '';
		var strCreditKey = '';
		var strCreditValue = '';
		var varsForm = {};
		var debitKey = null;
		var debitValue = null;
		var creditKey = null;
		var creditValue = null;

		var flagGeneral = this._getFlagGeneral();
		var flagConsumptionTaxDeducted = parseFloat(this.vars.varsDetail.varsEntityNation.flagConsumptionTaxDeducted);
		var arrayOption = [];
		var flagKey = '';
		if (flagGeneral) {
			if (flagConsumptionTaxDeducted) {
				arrayOption = this.vars.varsRule.varsConsumptionTax.generalEach;
				flagKey = 'flagConsumptionTaxGeneralRuleEach';

			} else {
				arrayOption = this.vars.varsRule.varsConsumptionTax.generalProration;
				flagKey = 'flagConsumptionTaxGeneralRuleProration';
			}

		} else {
			arrayOption = this.vars.varsRule.varsConsumptionTax.simple;
			flagKey = 'flagConsumptionTaxSimpleRule';
		}

		if (this.vars.varsRule.arrAccountTitle.arrStrTitle[obj.vars.arrDebit.idAccountTitle]) {
			if (!obj.vars.arrDebit.flagConsumptionTaxFree) {
				var cut = this.vars.varsRule.arrAccountTitle.arrStrTitle[obj.vars.arrDebit.idAccountTitle];
				var flagGeneral = this._getFlagGeneral();

				var flagConsumptionTaxRule = this._getFlagConsumptionTaxRule({
					flagDebit : 1,
					vars      : obj.vars,
					cut       : cut
				});

				if (flagConsumptionTaxRule == 'none') {
					strDebitKey = '';

				} else {
					if (flagGeneral) {
						if (flagConsumptionTaxDeducted) {
							strDebitKey = this.vars.varsRule.varsConsumptionTax.arrStrGeneralEach[flagConsumptionTaxRule];
						} else {
							strDebitKey = this.vars.varsRule.varsConsumptionTax.arrStrGeneralProration[flagConsumptionTaxRule];
						}

					} else {
						strDebitKey = this.vars.varsRule.varsConsumptionTax.arrStrSimple[flagConsumptionTaxRule];
					}
				}

				if (this.vars.varsStatus.flagEditUse) {
					debitKey = {
						flagTag       : 'selectShortCut',
						flagInputType : '',
						numMaxlength  : 0,
						numWidth      : 0,
						unitWidth     : 'px',
						numHeight     : 0,
						unitHeight    : 'px',
						arrayOption   : arrayOption,
						flagKey       : flagKey,
						flagDebit     : 1,
						value         : flagConsumptionTaxRule,
						vars          : obj.vars
					};
				}

				strDebitValue = this._getStrFlagConsumptionTaxWithoutCalc({
					flagDebit : 1,
					vars      : obj.vars,
					cut       : cut
				});
				if (strDebitValue != '') {
					var flagConsumptionTaxWithoutCalc = parseFloat(this.vars.varsDetail.varsEntityNation.flagConsumptionTaxWithoutCalc);
					if (this.vars.varsStatus.flagEditUse) {
						if (!obj.vars.arrDebit.flagConsumptionTaxWithoutCalc) {
							obj.vars.arrDebit.flagConsumptionTaxWithoutCalc = flagConsumptionTaxWithoutCalc;
						}
						debitValue = {
							flagTag       : 'selectShortCut',
							flagInputType : '',
							numMaxlength  : 0,
							numWidth      : 0,
							unitWidth     : 'px',
							numHeight     : 0,
							unitHeight    : 'px',
							arrayOption   : this.vars.varsTmpl.varsSelectTag.flagConsumptionTaxWithoutCalc,
							flagKey       : 'flagConsumptionTaxWithoutCalc',
							flagDebit     : 1,
							value         : obj.vars.arrDebit.flagConsumptionTaxWithoutCalc,
							vars          : obj.vars
						};
					}

				}
			}

			varsForm.debitKey = debitKey;
			varsForm.debitValue = debitValue;
		}
		if (this.vars.varsRule.arrAccountTitle.arrStrTitle[obj.vars.arrCredit.idAccountTitle]) {
			if (!obj.vars.arrCredit.flagConsumptionTaxFree) {
				var cut = this.vars.varsRule.arrAccountTitle.arrStrTitle[obj.vars.arrCredit.idAccountTitle];
				var flagGeneral = this._getFlagGeneral();
				var flagConsumptionTaxRule = this._getFlagConsumptionTaxRule({
					flagDebit : 0,
					vars      : obj.vars,
					cut       : cut
				});

				if (flagConsumptionTaxRule == 'none') {
					strCreditKey = '';

				} else {
					if (flagGeneral) {
						if (flagConsumptionTaxDeducted) {
							strCreditKey = this.vars.varsRule.varsConsumptionTax.arrStrGeneralEach[flagConsumptionTaxRule];
						} else {
							strCreditKey = this.vars.varsRule.varsConsumptionTax.arrStrGeneralProration[flagConsumptionTaxRule];
						}

					} else {
						strCreditKey = this.vars.varsRule.varsConsumptionTax.arrStrSimple[flagConsumptionTaxRule];
					}
				}
				if (this.vars.varsStatus.flagEditUse) {
					creditKey = {
						flagTag       : 'selectShortCut',
						flagInputType : '',
						numMaxlength  : 0,
						numWidth      : 0,
						unitWidth     : 'px',
						numHeight     : 0,
						unitHeight    : 'px',
						arrayOption   : arrayOption,
						flagKey       : flagKey,
						flagDebit     : 0,
						value         : flagConsumptionTaxRule,
						vars          : obj.vars
					};
				}

				strCreditValue = this._getStrFlagConsumptionTaxWithoutCalc({
					flagDebit : 0,
					vars      : obj.vars,
					cut       : cut
				});

				if (strCreditValue != '') {
					var flagConsumptionTaxWithoutCalc = parseFloat(this.vars.varsDetail.varsEntityNation.flagConsumptionTaxWithoutCalc);
					if (this.vars.varsStatus.flagEditUse) {
						if (!obj.vars.arrCredit.flagConsumptionTaxWithoutCalc) {
							obj.vars.arrCredit.flagConsumptionTaxWithoutCalc = flagConsumptionTaxWithoutCalc;
						}
						creditValue = {
							flagTag       : 'selectShortCut',
							flagInputType : '',
							numMaxlength  : 0,
							numWidth      : 0,
							unitWidth     : 'px',
							numHeight     : 0,
							unitHeight    : 'px',
							arrayOption   : this.vars.varsTmpl.varsSelectTag.flagConsumptionTaxWithoutCalc,
							flagKey       : 'flagConsumptionTaxWithoutCalc',
							flagDebit     : 0,
							value         : obj.vars.arrCredit.flagConsumptionTaxWithoutCalc,
							vars          : obj.vars
						};
					}

				}
			}

			varsForm.creditKey = creditKey;
			varsForm.creditValue = creditValue;
		}

		var varsBtnText = {
			flagRow         : 'subConsumptionTax',
			flagDebitKey    : 1,
			flagDebitValue  : 1,
			flagCreditKey   : 1,
			flagCreditValue : 1,
			arrDebit        : obj.vars.arrDebit,
			arrCredit       : obj.vars.arrCredit
		};

		this._setSeparateColumn({
			numArr         : obj.numArr,
			eleWrap        : obj.eleSubWrap,
			varsBtnText    : varsBtnText,
			strDebitKey    : strDebitKey,
			strDebitValue  : strDebitValue,
			strCreditKey   : strCreditKey,
			strCreditValue : strCreditValue,
			strClassBg     : 'codeLibBaseBgNoactive',
			varsForm       : varsForm
		});

	},

	/**
	 *
	 */
	_setDetailMainConsumptionTax : function(obj)
	{
		var insDisplayComma = new Code_Lib_DisplayComma();
		var strDebitKey = '';
		var strDebitValue = '';
		var strCreditKey = '';
		var strCreditValue = '';
		var numValueDebit = (!obj.vars.arrDebit.numValue)? null : parseFloat(obj.vars.arrDebit.numValue);
		var numValueCredit = (!obj.vars.arrCredit.numValue)? null : parseFloat(obj.vars.arrCredit.numValue);
		var numDebitKeyValue = '';
		var numCreditKeyValue = '';
		var varsForm = {};

		if (this.vars.varsRule.arrAccountTitle.arrStrTitle[obj.vars.arrDebit.idAccountTitle]) {
			var cut = this.vars.varsRule.arrAccountTitle.arrStrTitle[obj.vars.arrDebit.idAccountTitle];
			var flagConsumptionTaxRule = this._getFlagConsumptionTaxRule({
				flagDebit : 1,
				vars      : obj.vars,
				cut       : cut
			});
			var flagConsumptionTaxWithoutCalc = obj.vars.arrDebit.flagConsumptionTaxWithoutCalc;
			var numRateConsumptionTax = obj.vars.arrDebit.numRateConsumptionTax;

			if ((flagConsumptionTaxRule.match(/^tax/) || flagConsumptionTaxRule.match(/^else/))
				 && !obj.vars.arrDebit.flagConsumptionTaxFree
			) {
				strDebitKey = this.vars.varsTmpl.varsStrTitle.numRateConsumptionTax[numRateConsumptionTax];
				if (this.vars.varsStatus.flagEditUse) {
					varsForm.debitKey = {
						flagTag       : 'selectShortCut',
						flagInputType : '',
						numMaxlength  : 0,
						numWidth      : 0,
						unitWidth     : 'px',
						numHeight     : 0,
						unitHeight    : 'px',
						arrayOption   : this.vars.varsTmpl.varsSelectTag.numRateConsumptionTax,
						flagKey       : 'numRateConsumptionTax',
						flagDebit     : 1,
						value         : obj.vars.arrDebit.numRateConsumptionTax,
						vars          : obj.vars
					};
					var flagJson = (Object.toJSON(this.vars.varsTmpl.varsSelectTag.numRateConsumptionTax));
					if (flagJson == '[]' || flagJson == '{}') {
						varsForm.debitKey = null;
						strDebitKey = '';
					}
				}
			}

			if (obj.vars.arrDebit.numValue
				 && flagConsumptionTaxRule.match(/^tax/)
				 && obj.vars.arrDebit.idAccountTitle != 'suspenseReceiptOfConsumptionTaxes'
				 && obj.vars.arrDebit.idAccountTitle != 'suspensePaymentConsumptionTaxes'
				 && flagConsumptionTaxWithoutCalc != 3
				 && !obj.vars.arrDebit.flagConsumptionTaxIncluding
				 && !obj.vars.arrDebit.flagConsumptionTaxFree
				 && obj.vars.arrDebit.numValueConsumptionTax != ''
			) {
				if (!flagConsumptionTaxWithoutCalc) {
					flagConsumptionTaxWithoutCalc = parseFloat(this.vars.varsDetail.varsEntityNation.flagConsumptionTaxWithoutCalc);
				}
				var num = parseFloat(obj.vars.arrDebit.numValueConsumptionTax);
				numDebitKeyValue = num;
				if (flagConsumptionTaxWithoutCalc == 1) {
					if (numValueDebit) {
						strDebitValue = '( ' + insDisplayComma.get({num : num});
					}

				} else if (flagConsumptionTaxWithoutCalc == 2) {
					if (numValueDebit) {
						strDebitValue = insDisplayComma.get({num : num});
					}
				}

				if (flagConsumptionTaxWithoutCalc == 1 || flagConsumptionTaxWithoutCalc == 2) {
					if (this.vars.varsStatus.flagEditUse) {
						varsForm.debitValue = {
							flagTag       : 'input',
							flagInputType : 'text',
							numMaxlength  : 11,
							numWidth      : 0,
							unitWidth     : 'px',
							numHeight     : 0,
							unitHeight    : 'px',
							arrayOption   : this.vars.varsRule.arrAccountTitle.arrSelectTag,
							flagKey       : 'numValueConsumptionTax',
							flagDebit     : 1,
							value         : numDebitKeyValue,
							vars          : obj.vars
						};
						if (obj.vars.arrDebit.idAccountTitle == '' || !obj.vars.arrDebit.numValue) debitValue = null;
					}
				}
			}
		}
		if (this.vars.varsRule.arrAccountTitle.arrStrTitle[obj.vars.arrCredit.idAccountTitle]) {
			var cut = this.vars.varsRule.arrAccountTitle.arrStrTitle[obj.vars.arrCredit.idAccountTitle];
			var flagConsumptionTaxRule = this._getFlagConsumptionTaxRule({
				flagDebit : 0,
				vars      : obj.vars,
				cut       : cut
			});
			var flagConsumptionTaxWithoutCalc = obj.vars.arrCredit.flagConsumptionTaxWithoutCalc;
			var numRateConsumptionTax = obj.vars.arrCredit.numRateConsumptionTax;

			if ((flagConsumptionTaxRule.match(/^tax/) || flagConsumptionTaxRule.match(/^else/))
				 && !obj.vars.arrCredit.flagConsumptionTaxFree
			) {
				strCreditKey = this.vars.varsTmpl.varsStrTitle.numRateConsumptionTax[numRateConsumptionTax];
				if (this.vars.varsStatus.flagEditUse) {
					varsForm.creditKey = {
						flagTag       : 'selectShortCut',
						flagInputType : '',
						numMaxlength  : 0,
						numWidth      : 0,
						unitWidth     : 'px',
						numHeight     : 0,
						unitHeight    : 'px',
						arrayOption   : this.vars.varsTmpl.varsSelectTag.numRateConsumptionTax,
						flagKey       : 'numRateConsumptionTax',
						flagCredit     : 1,
						value         : obj.vars.arrCredit.numRateConsumptionTax,
						vars          : obj.vars
					};
					var flagJson = (Object.toJSON(this.vars.varsTmpl.varsSelectTag.numRateConsumptionTax));
					if (flagJson == '[]' || flagJson == '{}') {
						varsForm.creditKey = null;
						strCreditKey = '';
					}
				}
			}

			if (obj.vars.arrCredit.numValue
				 && flagConsumptionTaxRule.match(/^tax/)
				 && obj.vars.arrCredit.idAccountTitle != 'suspenseReceiptOfConsumptionTaxes'
				 && obj.vars.arrCredit.idAccountTitle != 'suspensePaymentConsumptionTaxes'
				 && flagConsumptionTaxWithoutCalc != 3
				 && !obj.vars.arrCredit.flagConsumptionTaxIncluding
				 && !obj.vars.arrCredit.flagConsumptionTaxFree
				 && obj.vars.arrCredit.numValueConsumptionTax != ''
			) {
				if (flagConsumptionTaxWithoutCalc == 0) {
					flagConsumptionTaxWithoutCalc = parseFloat(this.vars.varsDetail.varsEntityNation.flagConsumptionTaxWithoutCalc);
				}
				var num = parseFloat(obj.vars.arrCredit.numValueConsumptionTax);
				numCreditKeyValue = num;
				if (flagConsumptionTaxWithoutCalc == 1) {
					if (numValueCredit) {
						strCreditValue = '( ' + insDisplayComma.get({num : num});
					}

				} else if (flagConsumptionTaxWithoutCalc == 2) {
					if (numValueCredit) {
						strCreditValue = insDisplayComma.get({num : num});
					}
				}
				if (flagConsumptionTaxWithoutCalc == 1 || flagConsumptionTaxWithoutCalc == 2) {
					if (this.vars.varsStatus.flagEditUse) {
						varsForm.creditValue = {
							flagTag       : 'input',
							flagInputType : 'text',
							numMaxlength  : 11,
							numWidth      : 0,
							unitWidth     : 'px',
							numHeight     : 0,
							unitHeight    : 'px',
							arrayOption   : this.vars.varsRule.arrAccountTitle.arrSelectTag,
							flagKey       : 'numValueConsumptionTax',
							flagDebit     : 0,
							value         : numCreditKeyValue,
							vars          : obj.vars
						};
						if (obj.vars.arrCredit.idAccountTitle == '' || !obj.vars.arrCredit.numValue) creditValue = null;
					}
				}
			}
		}
		var varsBtnText = {
			flagRow         : 'mainConsumptionTax',
			flagDebitKey    : 1,
			flagDebitValue  : 0,
			flagCreditKey   : 1,
			flagCreditValue : 0,
			arrDebit        : obj.vars.arrDebit,
			arrCredit       : obj.vars.arrCredit
		};

		this._setSeparateColumn({
			numArr         : obj.numArr,
			eleWrap        : obj.eleMainWrap,
			varsBtnText    : varsBtnText,
			strDebitKey    : strDebitKey,
			strDebitValue  : strDebitValue,
			strCreditKey   : strCreditKey,
			strCreditValue : strCreditValue,
			varsForm       : varsForm
		});

	},

	/**
	 *
	 */
	_getFlagConsumptionTaxRuleDefault : function(obj)
	{
		var flagGeneral = parseFloat(this.vars.varsDetail.varsEntityNation.flagConsumptionTaxGeneralRule);

		var flagConsumptionTaxRule = 'none';
		if (flagGeneral) {
			flagConsumptionTaxRule = (obj.flagConsumptionTaxRule)? obj.flagConsumptionTaxRule : 'none';

		} else {
			if (obj.flagConsumptionTaxRule) {
				var flagConsumptionTaxBusinessType = parseFloat(this.vars.varsDetail.varsEntityNation.flagConsumptionTaxBusinessType);
				if (obj.flagConsumptionTaxRule == 'tax-Default') {
					flagConsumptionTaxRule = 'tax-' + flagConsumptionTaxBusinessType;

				} else if (obj.flagConsumptionTaxRule == 'tax-Back-Default') {
					flagConsumptionTaxRule = 'tax-Back-' + flagConsumptionTaxBusinessType;

				} else {
					flagConsumptionTaxRule = obj.flagConsumptionTaxRule;
				}

			} else {
				flagConsumptionTaxRule = 'none';
			}
		}

		return flagConsumptionTaxRule;
	},

	/**
	 *
	 */
	_resetVarsSide : function(obj)
	{
		var flagConsumptionTaxFree = parseFloat(this.vars.varsDetail.varsEntityNation.flagConsumptionTaxFree);
		var flagConsumptionTaxIncluding = parseFloat(this.vars.varsDetail.varsEntityNation.flagConsumptionTaxIncluding);
		var flagConsumptionTaxWithoutCalc = parseFloat(this.vars.varsDetail.varsEntityNation.flagConsumptionTaxWithoutCalc);
		var flagConsumptionTaxCalc = parseFloat(this.vars.varsDetail.varsEntityNation.flagConsumptionTaxCalc);

		var flagGeneral = parseFloat(this.vars.varsDetail.varsEntityNation.flagConsumptionTaxGeneralRule);
		var flagConsumptionTaxDeducted = parseFloat(this.vars.varsDetail.varsEntityNation.flagConsumptionTaxDeducted);

		for (var i = 0; i < obj.arr.length; i++) {
			var str = 'arrCredit';
			if (obj.flagDebit) str = 'arrDebit';
			if (flagConsumptionTaxFree || flagConsumptionTaxIncluding) {
				flagConsumptionTaxWithoutCalc = 1;
			}
			var data = {
				idAccountTitle                         : '',
				numValue                               : '',
				numValueConsumptionTax                 : '',
				idDepartment                           : '',
				idSubAccountTitle                      : '',
				flagConsumptionTaxFree                 : flagConsumptionTaxFree,
				flagConsumptionTaxIncluding            : flagConsumptionTaxIncluding,
				flagConsumptionTaxGeneralRuleProration : '',
				flagConsumptionTaxGeneralRuleEach      : '',
				flagConsumptionTaxSimpleRule           : '',
				flagConsumptionTaxWithoutCalc          : flagConsumptionTaxWithoutCalc,
				flagConsumptionTaxCalc                 : flagConsumptionTaxCalc
			};
			obj.arr[i][str] = data;
		}
	},

	/**
	 *
	 */
	stampBook : 0,
	setStampBook : function(obj)
	{
		this.stampBook = parseFloat(obj.stamp);
	},

	/**
	 *
	 */
	_getNumRateConsumptionTax : function()
	{
		/*
		 * 20191001 start
		 */
		/*自動税率挿入処理追加*/
		var stamp20191001 = 1569855600 * 1000;

		var insCheckTime = new Code_Lib_CheckTime();
		var numRate = insCheckTime.checkRateConsumptionTax({
			insTimeZone : this.insRoot.insTimeZone,
			stamp       : (this.stampBook)? this.stampBook : stamp20191001
		});
		/*
		if (numRate == 10) {
			numRate = 8;
		}*/

		/*
		 * 20191001 end
		 */

		return numRate;
	},

	/**
	 *
	 */
	_updateEditVars : function(obj)
	{
		var numRateConsumptionTax = this._getNumRateConsumptionTax();
		var flagConsumptionTaxFree = parseFloat(this.vars.varsDetail.varsEntityNation.flagConsumptionTaxFree);
		var flagConsumptionTaxIncluding = parseFloat(this.vars.varsDetail.varsEntityNation.flagConsumptionTaxIncluding);
		var flagConsumptionTaxWithoutCalc = parseFloat(this.vars.varsDetail.varsEntityNation.flagConsumptionTaxWithoutCalc);
		var flagConsumptionTaxCalc = parseFloat(this.vars.varsDetail.varsEntityNation.flagConsumptionTaxCalc);

		var numNetTarget = this.vars.varsDetail.numSumDebit - this.vars.varsDetail.numSumCredit;
		if (obj.vars.flagDebit) {
			numNetTarget = this.vars.varsDetail.numSumCredit - this.vars.varsDetail.numSumDebit;
		}
		if (numNetTarget < 0) {
			numNetTarget = 0;
		}

		var flagGeneral = parseFloat(this.vars.varsDetail.varsEntityNation.flagConsumptionTaxGeneralRule);
		var flagConsumptionTaxDeducted = parseFloat(this.vars.varsDetail.varsEntityNation.flagConsumptionTaxDeducted);

		var insCheckValue = new Code_Lib_CheckValue();
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.vars.vars.id == obj.arr[i].id) {
				var str = 'arrCredit';
				if (obj.vars.flagDebit) {
					str = 'arrDebit';
				}

				if (obj.vars.flagKey == 'idAccountTitle') {
					if (flagConsumptionTaxFree || flagConsumptionTaxIncluding) {
						flagConsumptionTaxWithoutCalc = 1;
					}

					if (obj.vars.value == '') {
						var data = {
							idAccountTitle                         : '',
							numValue                               : '',
							numValueConsumptionTax                 : '',
							numRateConsumptionTax                  : '',
							idDepartment                           : '',
							idSubAccountTitle                      : '',
							flagConsumptionTaxFree                 : flagConsumptionTaxFree,
							flagConsumptionTaxIncluding            : flagConsumptionTaxIncluding,
							flagConsumptionTaxGeneralRuleProration : '',
							flagConsumptionTaxGeneralRuleEach      : '',
							flagConsumptionTaxSimpleRule           : '',
							flagConsumptionTaxWithoutCalc          : flagConsumptionTaxWithoutCalc,
							flagConsumptionTaxCalc                 : flagConsumptionTaxCalc
						};
						obj.arr[i][str] = data;
						break;

					} else {
						if (obj.vars.value.match(/^=(.*?)$/)) {
							obj.vars.value = RegExp.$1;
						}
						this._setVarsRecentIdAccountTitle({idAccountTitle : obj.vars.value});

						var flagConsumptionTaxGeneralRuleEach = '';
						var flagConsumptionTaxGeneralRuleProration = '';
						if (flagGeneral) {
							if (flagConsumptionTaxDeducted) {
								flagConsumptionTaxGeneralRuleEach = this.vars.varsRule.arrAccountTitle.arrStrTitle[obj.vars.value].flagConsumptionTaxGeneralRuleEach;

							} else {
								flagConsumptionTaxGeneralRuleProration = this.vars.varsRule.arrAccountTitle.arrStrTitle[obj.vars.value].flagConsumptionTaxGeneralRuleProration;
							}
						}
						var flagConsumptionTaxSimpleRule = '';
						if (!flagGeneral) {
							flagConsumptionTaxSimpleRule = this._getFlagConsumptionTaxRuleDefault({
								flagConsumptionTaxRule : this.vars.varsRule.arrAccountTitle.arrStrTitle[obj.vars.value].flagConsumptionTaxSimpleRule
							});
						}

						obj.arr[i][str].numValueConsumptionTax = '';
						obj.arr[i][str].numRateConsumptionTax = '';
						obj.arr[i][str].flagConsumptionTaxFree = flagConsumptionTaxFree;
						obj.arr[i][str].flagConsumptionTaxIncluding = flagConsumptionTaxIncluding;
						obj.arr[i][str].idSubAccountTitle = '';
						obj.arr[i][str].flagConsumptionTaxGeneralRuleEach = flagConsumptionTaxGeneralRuleEach;
						obj.arr[i][str].flagConsumptionTaxGeneralRuleProration = flagConsumptionTaxGeneralRuleProration;
						obj.arr[i][str].flagConsumptionTaxSimpleRule = flagConsumptionTaxSimpleRule;
						obj.arr[i][str].flagConsumptionTaxWithoutCalc = flagConsumptionTaxWithoutCalc;
						obj.arr[i][str].flagConsumptionTaxCalc = flagConsumptionTaxCalc;
						obj.arr[i][str][obj.vars.flagKey] = obj.vars.value;

						var flagConsumptionTaxRule = this._getFlagConsumptionTaxRule({
							flagDebit : obj.vars.flagDebit,
							vars      : obj.arr[i]
						});

						if ((obj.arr[i][str].numValue == '' || obj.arr[i][str].numValue == 0) && numNetTarget) {
							if (!(flagConsumptionTaxRule.match(/^tax/) && flagConsumptionTaxWithoutCalc == 2)) {
								obj.arr[i][str].numValue = numNetTarget;
							}
						}
						if (!flagConsumptionTaxFree
							&& (flagConsumptionTaxRule.match(/^tax/) || flagConsumptionTaxRule.match(/^else/))
						) {
							obj.arr[i][str].numRateConsumptionTax = numRateConsumptionTax;
						} else {
							obj.arr[i][str].numRateConsumptionTax = '';
						}
						obj.arr[i][str].numValueConsumptionTax = this._getNumValueConsumptionTax({
							flagDebit : obj.vars.flagDebit,
							vars      : obj.arr[i]
						});
						this._checkContraNumValue({
							vars    : obj.vars,
							varsArr : obj.arr[i]
						});
						break;
					}

				} else if (obj.vars.flagKey == 'numValue') {
					obj.vars.value = this._checkNumValue({
						value : obj.vars.value
					});
					obj.arr[i][str][obj.vars.flagKey] = obj.vars.value;
					obj.arr[i][str].numValueConsumptionTax = this._getNumValueConsumptionTax({
						flagDebit : obj.vars.flagDebit,
						vars      : obj.arr[i]
					});

				} else if (obj.vars.flagKey.match(/^flagConsumptionTaxGeneralRule/)) {
					obj.arr[i][str][obj.vars.flagKey] = obj.vars.value;
					if (!flagConsumptionTaxFree
						&& (obj.vars.value.match(/^tax/) || obj.vars.value.match(/^else/))
					) {
						obj.arr[i][str].numRateConsumptionTax = numRateConsumptionTax;
					} else {
						obj.arr[i][str].numRateConsumptionTax = '';
					}
					obj.arr[i][str].numValueConsumptionTax = this._getNumValueConsumptionTax({
						flagDebit : obj.vars.flagDebit,
						vars      : obj.arr[i]
					});

				} else if (obj.vars.flagKey == 'flagConsumptionTaxSimpleRule') {
					obj.arr[i][str][obj.vars.flagKey] = obj.vars.value;
					if (!flagConsumptionTaxFree
						&& (obj.vars.value.match(/^tax/) || obj.vars.value.match(/^else/))
					) {
						obj.arr[i][str].numRateConsumptionTax = numRateConsumptionTax;
					} else {
						obj.arr[i][str].numRateConsumptionTax = '';
					}
					obj.arr[i][str].numValueConsumptionTax = this._getNumValueConsumptionTax({
						flagDebit : obj.vars.flagDebit,
						vars      : obj.arr[i]
					});

				} else if (obj.vars.flagKey == 'flagConsumptionTaxWithoutCalc') {
					obj.arr[i][str][obj.vars.flagKey] = obj.vars.value;
					obj.arr[i][str].numValueConsumptionTax = this._getNumValueConsumptionTax({
						flagDebit : obj.vars.flagDebit,
						vars      : obj.arr[i]
					});

				} else if (obj.vars.flagKey == 'numRateConsumptionTax') {
					obj.arr[i][str][obj.vars.flagKey] = obj.vars.value;
					obj.arr[i][str].numValueConsumptionTax = this._getNumValueConsumptionTax({
						flagDebit : obj.vars.flagDebit,
						vars      : obj.arr[i]
					});

				} else if (obj.vars.flagKey == 'numValueConsumptionTax') {
					obj.vars.value = this._checkNumValue({
						value : obj.vars.value
					});
					if (!obj.vars.value) {
						obj.vars.value = this._getNumValueConsumptionTax({
							flagDebit : obj.vars.flagDebit,
							vars      : obj.arr[i]
						});
						obj.arr[i][str].numValueConsumptionTax = obj.vars.value;
					}
					obj.arr[i][str][obj.vars.flagKey] = obj.vars.value;
					if (obj.arr[i][str].numValue && obj.arr[i][str][obj.vars.flagKey]) {
						var numValue = parseFloat(obj.arr[i][str].numValue);
						var numValueConsumptionTax = parseFloat(obj.arr[i][str][obj.vars.flagKey]);
						if (numValue < numValueConsumptionTax) {
							obj.arr[i][str][obj.vars.flagKey] = this._getNumValueConsumptionTax({
								flagDebit : obj.vars.flagDebit,
								vars      : obj.arr[i]
							});
						}
					}

				} else {
					obj.arr[i][str][obj.vars.flagKey] = obj.vars.value;
				}
				this._checkContraNumValue({
					vars    : obj.vars,
					varsArr : obj.arr[i]
				});
				break;
			}
		}
		this.iniReload();
	},

	/**
	 *
	*/
	_checkContraNumValue : function(obj)
	{
		var str = 'arrCredit';
		var strContra = 'arrDebit';
		if (obj.vars.flagDebit) {
			str = 'arrDebit';
			strContra = 'arrCredit';
		}
		var flagConsumptionTaxRuleTarget = this._getFlagConsumptionTaxRule({
			flagDebit : obj.vars.flagDebit,
			vars      : obj.varsArr
		});
		var flagConsumptionTaxRuleContra = this._getFlagConsumptionTaxRule({
			flagDebit : (obj.vars.flagDebit)? 0 : 1,
			vars      : obj.varsArr
		});

		if (this.vars.varsDetail.varsDetail.length == 1
			&& obj.varsArr[strContra].idAccountTitle
		) {
			if (!(flagConsumptionTaxRuleContra.match(/^tax/) && obj.varsArr[strContra].flagConsumptionTaxWithoutCalc == 2)) {
				if (flagConsumptionTaxRuleTarget.match(/^tax/) && obj.varsArr[str].flagConsumptionTaxWithoutCalc == 2) {
					obj.varsArr[strContra].numValue = parseFloat(obj.varsArr[str].numValue) + parseFloat(obj.varsArr[str].numValueConsumptionTax);
					if (!obj.varsArr[strContra].numValueConsumptionTax) {
						obj.varsArr[strContra].numValueConsumptionTax = this._getNumValueConsumptionTax({
							flagDebit : (obj.vars.flagDebit)? 0 : 1,
							vars      : obj.varsArr
						});
					}

				} else {
					obj.varsArr[strContra].numValue = obj.varsArr[str].numValue;
					if (!obj.varsArr[strContra].numValueConsumptionTax) {
						obj.varsArr[strContra].numValueConsumptionTax = this._getNumValueConsumptionTax({
							flagDebit : (obj.vars.flagDebit)? 0 : 1,
							vars      : obj.varsArr
						});
					}
				}
			}
		}
	},

	/**
	 *
	*/
	_checkNumValue : function(obj)
	{
		var insEscape = new Code_Lib_Escape();
		var insCheck = new Code_Lib_CheckValue();

		var strValue = obj.value + '';

		if (strValue === '') {
			return '';
		}

		if (strValue.match(/,/)) {
			var arr = strValue.split(',');
			strValue = arr.join('');
		}

		strValue = insEscape.get({
			flagType : 'strToNum',
			data     : strValue
		});

		var flag = insCheck.checkValueWord({
			flagType : 'num',
			value    : strValue
		});

		if (flag) return '';

		return parseFloat(strValue);
	},

	/**
	 *
	 */
	_getConsumptionTax : function(obj)
	{
		var numRateConsumptionTax = parseFloat(obj.numRateConsumptionTax);

		var flagConsumptionTaxCalc = parseFloat(this.vars.varsDetail.varsEntityNation.flagConsumptionTaxCalc);
		if (obj.flagConsumptionTaxCalc) flagConsumptionTaxCalc = parseFloat(obj.flagConsumptionTaxCalc);
		var numValue = 0;
		if (obj.flagIn) numValue = obj.numValue * numRateConsumptionTax / (100 + numRateConsumptionTax);
		else numValue = obj.numValue * numRateConsumptionTax / 100;

		if (flagConsumptionTaxCalc == 1) numValue = Math.floor(numValue);
		else if (flagConsumptionTaxCalc == 2) numValue = Math.round(numValue);
		else if (flagConsumptionTaxCalc == 3) numValue = Math.ceil(numValue);

		return numValue;
	},

	/**
	 *
	 */
	_getNumValueConsumptionTax : function(obj)
	{
		var str = 'arrCredit';
		if (obj.flagDebit) str = 'arrDebit';
		var numValueConsumptionTax = '';
		var numValue = (!obj.vars[str].numValue)? null : parseFloat(obj.vars[str].numValue);

		if (this.vars.varsRule.arrAccountTitle.arrStrTitle[obj.vars[str].idAccountTitle]) {
			var cut = this.vars.varsRule.arrAccountTitle.arrStrTitle[obj.vars[str].idAccountTitle];
			var flagConsumptionTaxRule = this._getFlagConsumptionTaxRule({
				flagDebit : obj.flagDebit,
				vars      : obj.vars,
				cut       : cut
			});
			var flagConsumptionTaxWithoutCalc = obj.vars[str].flagConsumptionTaxWithoutCalc;
			if (obj.vars[str].numValue
				 && flagConsumptionTaxRule.match(/^tax/)
				 && obj.vars[str].idAccountTitle != 'suspenseReceiptOfConsumptionTaxes'
				 && obj.vars[str].idAccountTitle != 'suspensePaymentConsumptionTaxes'
				 && flagConsumptionTaxWithoutCalc != 3
				 && !obj.vars[str].flagConsumptionTaxIncluding
				 && !obj.vars[str].flagConsumptionTaxFree
			) {
				if (!flagConsumptionTaxWithoutCalc) {
					flagConsumptionTaxWithoutCalc = parseFloat(this.vars.varsDetail.varsEntityNation.flagConsumptionTaxWithoutCalc);
				}

				if (flagConsumptionTaxWithoutCalc == 1) {
					if (numValue) {
						numValueConsumptionTax = this._getConsumptionTax({
							flagIn                 : 1,
							numValue               : numValue,
							flagConsumptionTaxCalc : obj.vars[str].flagConsumptionTaxCalc,
							numRateConsumptionTax  : obj.vars[str].numRateConsumptionTax
						});
					}

				} else if (flagConsumptionTaxWithoutCalc == 2) {
					if (numValue) {
						numValueConsumptionTax = this._getConsumptionTax({
							flagIn                 : 0,
							numValue               : numValue,
							flagConsumptionTaxCalc : obj.vars[str].flagConsumptionTaxCalc,
							numRateConsumptionTax  : obj.vars[str].numRateConsumptionTax
						});
					}
				}
			}
		}
		return numValueConsumptionTax;
	},

	/**
	 *
	 */
	_setDetailMainAccount : function(obj)
	{
		var insDisplayComma = new Code_Lib_DisplayComma();
		var strDebitKey = '';
		var strDebitValue = '';
		var strCreditKey = '';
		var strCreditValue = '';
		var varsForm = {};
		var numDebitKeyValue = '';
		var numCreditKeyValue = '';

		if (this.vars.varsRule.arrAccountTitle.arrStrTitle[obj.vars.arrDebit.idAccountTitle]) {
			strDebitKey = this.vars.varsRule.arrAccountTitle.arrStrTitle[obj.vars.arrDebit.idAccountTitle].strTitleFS;
			if (!obj.vars.arrDebit.numValue) {
				numDebitKeyValue = '';
				obj.vars.arrDebit.numValue = '';

			} else {
				obj.vars.arrDebit.numValue = parseFloat(obj.vars.arrDebit.numValue);
				numDebitKeyValue = obj.vars.arrDebit.numValue;
				strDebitValue = insDisplayComma.get({
					num : obj.vars.arrDebit.numValue
				});
			}
		}

		if (this.vars.varsRule.arrAccountTitle.arrStrTitle[obj.vars.arrCredit.idAccountTitle]) {
			strCreditKey = this.vars.varsRule.arrAccountTitle.arrStrTitle[obj.vars.arrCredit.idAccountTitle].strTitleFS;
			if (!obj.vars.arrCredit.numValue) {
				numCreditKeyValue = '';
				obj.vars.arrCredit.numValue = '';

			} else {
				obj.vars.arrCredit.numValue = parseFloat(obj.vars.arrCredit.numValue);
				numCreditKeyValue = obj.vars.arrCredit.numValue;
				strCreditValue = insDisplayComma.get({
					num : obj.vars.arrCredit.numValue
				});
			}
		}

		if (this.vars.varsStatus.flagEditUse) {
			var debitKey = {
				flagTag       : 'selectShortCut',
				flagInputType : '',
				numMaxlength  : 0,
				numWidth      : 0,
				unitWidth     : 'px',
				numHeight     : 0,
				unitHeight    : 'px',
				arrayOption   : this.vars.varsRule.arrAccountTitle.arrSelectTag,
				flagKey       : 'idAccountTitle',
				flagDebit     : 1,
				value         : obj.vars.arrDebit.idAccountTitle,
				vars          : obj.vars
			};
			var debitValue = {
				flagTag       : 'input',
				flagInputType : 'text',
				numMaxlength  : 11,
				numWidth      : 0,
				unitWidth     : 'px',
				numHeight     : 0,
				unitHeight    : 'px',
				arrayOption   : this.vars.varsRule.arrAccountTitle.arrSelectTag,
				flagKey       : 'numValue',
				flagDebit     : 1,
				value         : numDebitKeyValue,
				vars          : obj.vars
			};
			if (obj.vars.arrDebit.idAccountTitle == '') debitValue = null;
			var creditKey = {
				flagTag       : 'selectShortCut',
				flagInputType : '',
				numMaxlength  : 0,
				numWidth      : 0,
				unitWidth     : 'px',
				numHeight     : 0,
				unitHeight    : 'px',
				arrayOption   : this.vars.varsRule.arrAccountTitle.arrSelectTag,
				flagKey       : 'idAccountTitle',
				flagDebit     : 0,
				value         : obj.vars.arrCredit.idAccountTitle,
				vars          : obj.vars
			};
			var creditValue = {
				flagTag       : 'input',
				flagInputType : 'text',
				numMaxlength  : 11,
				numWidth      : 0,
				unitWidth     : 'px',
				numHeight     : 0,
				unitHeight    : 'px',
				arrayOption   : this.vars.varsRule.arrAccountTitle.arrSelectTag,
				flagKey       : 'numValue',
				flagDebit     : 0,
				value         : numCreditKeyValue,
				vars          : obj.vars
			};
			if (obj.vars.arrCredit.idAccountTitle == '') creditValue = null;

			varsForm = {
				debitKey     : debitKey,
				debitValue   : debitValue,
				creditKey    : creditKey,
				creditValue  : creditValue
			};

		}

		var varsBtnText = {
			flagRow         : 'mainAccount',
			flagDebitKey    : 1,
			flagDebitValue  : 0,
			flagCreditKey   : 1,
			flagCreditValue : 0,
			arrDebit        : obj.vars.arrDebit,
			arrCredit       : obj.vars.arrCredit
		};

		this._setSeparateColumn({
			numArr         : obj.numArr,
			eleWrap        : obj.eleMainWrap,
			varsBtnText    : varsBtnText,
			strDebitKey    : strDebitKey,
			strDebitValue  : strDebitValue,
			strCreditKey   : strCreditKey,
			strCreditValue : strCreditValue,
			varsForm       : varsForm
		});

	},



	/**
	 *
	 */
	_mousedownSortUp : function(evt, obj)
	{
		if (obj) evt.stop();
		else obj = evt;

		if (obj.vars.id == 0) return;
		this._varsBlock = {};

		this._getBlock({
			arr      : this.vars.varsDetail.varsDetail,
			idTarget : obj.vars.id
		});
		this.vars.varsDetail.varsDetail = this._removeBlock({
			arr      : this.vars.varsDetail.varsDetail,
			idTarget : obj.vars.id
		});
		if (obj.vars.id == 1) {
			this.vars.varsDetail.varsDetail.unshift(this._varsBlock);

		} else {
			this.vars.varsDetail.varsDetail = this._setBlockMove({
				arr      : this.vars.varsDetail.varsDetail,
				idTarget : obj.vars.id - 2,
				block    : this._varsBlock
			});
		}


		this.iniReload();
	},

	/**
	 *
	 */
	_mousedownSortDown : function(evt, obj)
	{
		if (obj) evt.stop();
		else obj = evt;

		var numArray = this.vars.varsDetail.varsDetail.length - 1;
		if (obj.vars.id == numArray) return;
		this._varsBlock = {};

		this._getBlock({
			arr      : this.vars.varsDetail.varsDetail,
			idTarget : obj.vars.id
		});

		this.vars.varsDetail.varsDetail = this._removeBlock({
			arr      : this.vars.varsDetail.varsDetail,
			idTarget : obj.vars.id
		});

		this.vars.varsDetail.varsDetail = this._setBlockMove({
			arr      : this.vars.varsDetail.varsDetail,
			idTarget : obj.vars.id + 1,
			block    : this._varsBlock
		});

		this.iniReload();
	},

	/**
	 *
	 */
	_mousedownSortSide : function(evt, obj)
	{
		if (obj) evt.stop();
		else obj = evt;

		this.vars.varsDetail.varsDetail = this._updateSortSide({
			arr  : this.vars.varsDetail.varsDetail,
			vars : obj.vars
		});
		this.iniReload();
	},

	/**
	 *
	 */
	_updateSortSide : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == obj.vars.id) {
				var dataDebit = (Object.toJSON(obj.arr[i].arrDebit)).evalJSON();
				var dataCredit = (Object.toJSON(obj.arr[i].arrCredit)).evalJSON();
				obj.arr[i].arrDebit = dataCredit;
				obj.arr[i].arrCredit = dataDebit;
				return obj.arr;
			}
		}
		return obj.arr;
	},

	/**
	 *
	 */
	_mousedownRemove : function(evt, obj)
	{
		if (obj) evt.stop();
		else obj = evt;
		this.vars.varsDetail.varsDetail = this._updateRemoveVars({
			arr  : this.vars.varsDetail.varsDetail,
			vars : obj.vars
		});
		if (!this.vars.varsDetail.varsDetail.length) {
			this._updateBarAdd();
		}
		this.iniReload();
	},

	/**
	 *
	 */
	_updateRemoveVars : function(obj)
	{
		var arrayNew = [];
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == obj.vars.id) continue;
			arrayNew.push(obj.arr[i]);
		}

		return arrayNew;
	},

	/**
	 *
	 */
	_mousedownCopy : function(evt, obj)
	{
		if (obj) evt.stop();
		else obj = evt;

		this.vars.varsDetail.varsDetail = this._updateCopyVars({
			arr  : this.vars.varsDetail.varsDetail,
			vars : obj.vars
		});
		this.iniReload();
	},

	/**
	 *
	 */
	_updateCopyVars : function(obj)
	{
		var data = null;
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == obj.vars.id) {
				data = (Object.toJSON(obj.arr[i])).evalJSON();
				break;
			}
		}
		data.id = new Date().getTime();
		obj.arr.push(data);

		return obj.arr;
	},

	/**
	 *
	*/
	getValue : function()
	{
		var data = (Object.toJSON(this.vars.varsDetail)).evalJSON();

		return data;
	},

	/**
	 *
	 */
	eleWrapDetail : null,
	_setDetailWrap : function()
	{
		var ele = $(document.createElement('div'));
		ele.addClassName('codePluginAccountingLibWrapDetail');
		this.eleWrap.insert(ele);
		this.eleWrapDetail = ele;
	}
});





<?php }
}
?>