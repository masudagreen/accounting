<?php /* Smarty version 3.1.24, created on 2016-08-18 12:51:01
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/plugin/accounting/js/lib/journalImport.js" */ ?>
<?php
/*%%SmartyHeaderCode:185317486357b5af35e417a6_45589072%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f9bba4e053f85d5ce236163268c25c9568339f50' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/plugin/accounting/js/lib/journalImport.js',
      1 => 1471523679,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '185317486357b5af35e417a6_45589072',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_57b5af35e8e764_11327667',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_57b5af35e8e764_11327667')) {
function content_57b5af35e8e764_11327667 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '185317486357b5af35e417a6_45589072';
?>

/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Plugin_Accounting_Lib_JournalImport = Class.create(Code_Plugin_Accounting_Lib_Journal,
{
	/**
	 *
	 */
	_updateEditVars : function(obj)
	{
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
				if (obj.vars.flagDebit) str = 'arrDebit';

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
					obj.arr[i][str].numValueConsumptionTax = this._getNumValueConsumptionTax({
						flagDebit : obj.vars.flagDebit,
						vars      : obj.arr[i]
					});

				} else if (obj.vars.flagKey == 'flagConsumptionTaxSimpleRule') {
					obj.arr[i][str][obj.vars.flagKey] = obj.vars.value;
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
		var varsBtnText = null;

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
			strClassBg     : (this.vars.varsStatus.flagEditUse)? 'codeLibBaseBgNoactive' : '',
			varsForm       : varsForm
		});

	}
});
<?php }
}
?>