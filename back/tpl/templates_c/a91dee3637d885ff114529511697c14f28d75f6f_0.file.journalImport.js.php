<?php /* Smarty version 3.1.24, created on 2022-08-13 00:24:08
         compiled from "/var/www/html/accounting/back/tpl/templates/else/plugin/accounting/js/lib/journalImport.js" */ ?>
<?php
/*%%SmartyHeaderCode:189287324962f6ef28ce0293_02549905%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a91dee3637d885ff114529511697c14f28d75f6f' => 
    array (
      0 => '/var/www/html/accounting/back/tpl/templates/else/plugin/accounting/js/lib/journalImport.js',
      1 => 1569568870,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '189287324962f6ef28ce0293_02549905',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_62f6ef28cf9575_90804437',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_62f6ef28cf9575_90804437')) {
function content_62f6ef28cf9575_90804437 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '189287324962f6ef28ce0293_02549905';
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