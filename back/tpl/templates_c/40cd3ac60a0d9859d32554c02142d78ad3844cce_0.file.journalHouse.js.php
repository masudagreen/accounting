<?php /* Smarty version 3.1.24, created on 2019-06-16 09:02:21
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/plugin/accounting/js/lib/journalHouse.js" */ ?>
<?php
/*%%SmartyHeaderCode:3359955225d06059d69c055_94863015%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '40cd3ac60a0d9859d32554c02142d78ad3844cce' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/plugin/accounting/js/lib/journalHouse.js',
      1 => 1560675148,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '3359955225d06059d69c055_94863015',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_5d06059d6a16a1_54014220',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_5d06059d6a16a1_54014220')) {
function content_5d06059d6a16a1_54014220 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '3359955225d06059d69c055_94863015';
?>

/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Plugin_Accounting_Lib_JournalHouse = Class.create(Code_Plugin_Accounting_Lib_Journal,
{

	/**
	 *
	 */
	_updateBarAdd : function()
	{
		var data = (Object.toJSON(this.vars.varsTmpl.varsDetailVarsDetail)).evalJSON();

		data.id = new Date().getTime();
		this.vars.varsDetail.varsDetail.push(data);
		var keyDebit = 'accountsReceivables';
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
						if (obj.varsBtnText.flagDebit) {
							return ele;
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
	_getEditVars : function(obj)
	{
		var varsStyle = this.allot({
			insCurrent : this.insCurrent,
			from       : '_getEditVars'
		});

		if (obj.vars.flagKey == 'idAccountTitle') {
			obj.vars.arrayOption = this.vars.varsRule.arrAccountTitleCost.arrSelectTag;
		}

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
	_getConsumptionTax : function(obj)
	{
		return '';
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

			varsForm.debitKey = null;
			varsForm.debitValue = null;
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
				varsForm.debitKey = null;
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
				varsForm.debitValue = null;
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
			var debitValue = null;
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
			var creditValue = null;
			if (obj.vars.arrCredit.idAccountTitle == '') creditValue = null;

			varsForm = {
				debitKey     : null,
				debitValue   : null,
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
			strClassBg     : (this.vars.varsStatus.flagEditUse)? 'codeLibBaseBgNoactive' : '',
			varsForm       : varsForm
		});

	}
});
<?php }
}
?>