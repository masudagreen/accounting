<?php /* Smarty version 3.1.24, created on 2016-08-18 12:51:01
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/plugin/accounting/js/lib/journalCash.js" */ ?>
<?php
/*%%SmartyHeaderCode:198888395957b5af35cdbfb2_91237514%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '84e5df15b9c7599be9723cd3b8e1d9af4a39a969' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/plugin/accounting/js/lib/journalCash.js',
      1 => 1471523679,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '198888395957b5af35cdbfb2_91237514',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_57b5af35d60ec9_80739773',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_57b5af35d60ec9_80739773')) {
function content_57b5af35d60ec9_80739773 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '198888395957b5af35cdbfb2_91237514';
?>

/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Plugin_Accounting_Lib_JournalCash = Class.create(Code_Plugin_Accounting_Lib_Journal,
{

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
		this.flagIn = parseFloat(obj.vars.flagIn);
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

		var arr = [1 , 0];
		for (var i = 0; i < arr.length; i++) {
			var flagDebit = arr[i];
			var flag = this._checkJsonDetail({
				flagDebit : flagDebit,
				flagIn    : this.flagIn,
				arr       : this.vars.varsDetail.varsDetail
			});
			if (flag) {
				this._resetVarsSide({
					flagDebit : flagDebit,
					arr       : this.vars.varsDetail.varsDetail
				});
			}
		}
		this._iniCake();
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
			if (this.vars.varsRule.arrAccountTitleCash.arrStrTitle[idTarget]) {
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

	_mousedownBtnDictionary : function(obj)
	{
		var arrayOption = [];
		if (parseFloat(this.flagIn) == 1) {
			arrayOption = this.vars.varsTmpl.varsSelectTag.varsBtnDictionaryIn;

		} else if (parseFloat(this.flagIn) == 2) {
			arrayOption = this.vars.varsTmpl.varsSelectTag.varsBtnDictionaryMove;

		} else {
			arrayOption = this.vars.varsTmpl.varsSelectTag.varsBtnDictionaryOut;
		}

		var vars = {
			flagTag       : 'selectShortCut',
			flagInputType : '',
			numMaxlength  : 0,
			numWidth      : 0,
			unitWidth     : 'px',
			numHeight     : 0,
			unitHeight    : 'px',
			arrayOption   : arrayOption,
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
	_checkJsonDetail : function(obj)
	{
		var cut = this.vars.varsRule;

		var arrSide = ['arrDebit', 'arrCredit'];
		for (var i = 0; i < obj.arr.length; i++) {

			for (var j = 0; j < arrSide.length; j++) {
				var strSide = arrSide[j];
				if (obj.flagDebit) {
					if (strSide == 'arrCredit') {
						continue;
					}
				} else {
					if (strSide == 'arrDebit') {
						continue;
					}
				}

				var idAccountTitle = obj.arr[i][strSide].idAccountTitle;
				var idDepartment = obj.arr[i][strSide].idDepartment;
				var idSubAccountTitle = obj.arr[i][strSide].idSubAccountTitle;

				if (idAccountTitle) {
					if (!cut.arrAccountTitle.arrStrTitle[idAccountTitle]) {
						return 'strOldIdAccountTitle';
					}

					if (idDepartment) {
						if (!cut.arrDepartment.arrSelectTag.length > 1) {
							return 'strOldIdDepartment';

						} else {
							if (!cut.arrDepartment.arrStrTitle[idDepartment]) {
								return 'strOldIdDepartment';
							}
						}
					}

					if (idSubAccountTitle) {
						if (!cut.arrSubAccountTitle.arrStrTitle[idAccountTitle]) {
							return 'strOldIdSubAccountTitle';

						} else {
							if (!cut.arrSubAccountTitle.arrStrTitle[idAccountTitle][idSubAccountTitle]) {
								return 'strOldIdSubAccountTitle';
							}
						}
					}

					if (strSide == 'arrDebit') {
						if (obj.flagIn == 1 || obj.flagIn == 2) {
							if (!cut.arrAccountTitleCash.arrStrTitle[idAccountTitle]) {
								return 'strOldIdAccountTitleCash';
							}
						}
					} else {
						if (obj.flagIn == 0 || obj.flagIn == 2) {
							if (!cut.arrAccountTitleCash.arrStrTitle[idAccountTitle]) {
								return 'strOldIdAccountTitleCash';
							}
						}
					}
				}
			}
		}

		return '';
	},

	/**
	 *
	 */
	updateFlagIn : function(obj)
	{
		if ((parseFloat(this.flagIn) == 1 || parseFloat(this.flagIn) == 0)
			&& (parseFloat(obj.flagIn) == 1 || parseFloat(obj.flagIn) == 0)
		) {
			this.flagIn = parseFloat(obj.flagIn);
			this.vars.varsDetail.varsDetail = this._updateSortSideData({
				arr  : this.vars.varsDetail.varsDetail,
				vars : obj.vars
			});

		} else {
			this.resetVarsDetail();
			this.flagIn = parseFloat(obj.flagIn);
		}

		this._valueBtnDictionary = '';
		this.iniReload();
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

				if (this.vars.varsStatus.flagEditUse && parseFloat(this.flagIn) == 0) {
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
				if (this.vars.varsStatus.flagEditUse && parseFloat(this.flagIn) == 1) {
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
			strClassBg     : (this.vars.varsStatus.flagEditUse)? 'codeLibBaseBgNoactive' : '',
			varsForm       : varsForm
		});

	},


	/**
	 *
	 */
	_updateSortSideData : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			var dataDebit = (Object.toJSON(obj.arr[i].arrDebit)).evalJSON();
			var dataCredit = (Object.toJSON(obj.arr[i].arrCredit)).evalJSON();
			obj.arr[i].arrDebit = dataCredit;
			obj.arr[i].arrCredit = dataDebit;
			return obj.arr;
		}
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
						if (parseFloat(this.flagIn) == 1 || parseFloat(this.flagIn) == 2) {
							if (obj.vars.flagDebit) {
								var flagConsumptionTaxGeneralRuleEach = '';
								var flagConsumptionTaxGeneralRuleProration = '';
								if (flagGeneral) {
									if (flagConsumptionTaxDeducted) {
										flagConsumptionTaxGeneralRuleEach = 'none';
									} else {
										flagConsumptionTaxGeneralRuleProration = 'none';
									}
								}
								var flagConsumptionTaxSimpleRule = '';
								if (!flagGeneral) {
									flagConsumptionTaxSimpleRule = 'none';
								}
								var data = {
									idAccountTitle                         : obj.vars.value,
									numValue                               : '',
									numValueConsumptionTax                 : '',
									numRateConsumptionTax                  : '',
									idDepartment                           : '',
									idSubAccountTitle                      : '',
									flagConsumptionTaxFree                 : flagConsumptionTaxFree,
									flagConsumptionTaxIncluding            : flagConsumptionTaxIncluding,
									flagConsumptionTaxGeneralRuleProration : flagConsumptionTaxGeneralRuleProration,
									flagConsumptionTaxGeneralRuleEach      : flagConsumptionTaxGeneralRuleEach,
									flagConsumptionTaxSimpleRule           : flagConsumptionTaxSimpleRule,
									flagConsumptionTaxWithoutCalc          : flagConsumptionTaxWithoutCalc,
									flagConsumptionTaxCalc                 : flagConsumptionTaxCalc
								};
								obj.arr[i][str] = data;
								if ((obj.arr[i][str].numValue == '' || obj.arr[i][str].numValue == 0) && numNetTarget) {
									obj.arr[i][str].numValue = numNetTarget;
								}
								this._checkContraNumValue({
									vars    : obj.vars,
									varsArr : obj.arr[i]
								});
								break;
							}

						} else if (parseFloat(this.flagIn) == 0 || parseFloat(this.flagIn) == 2) {
							if (!obj.vars.flagDebit) {
								var flagConsumptionTaxGeneralRuleEach = '';
								var flagConsumptionTaxGeneralRuleProration = '';
								if (flagGeneral) {
									if (flagConsumptionTaxDeducted) {
										flagConsumptionTaxGeneralRuleEach = 'none';
									} else {
										flagConsumptionTaxGeneralRuleProration = 'none';
									}
								}
								var flagConsumptionTaxSimpleRule = '';
								if (!flagGeneral) {
									flagConsumptionTaxSimpleRule = 'none';
								}
								var data = {
									idAccountTitle                         : obj.vars.value,
									numValue                               : '',
									numValueConsumptionTax                 : '',
									numRateConsumptionTax                  : '',
									idDepartment                           : '',
									idSubAccountTitle                      : '',
									flagConsumptionTaxFree                 : flagConsumptionTaxFree,
									flagConsumptionTaxIncluding            : flagConsumptionTaxIncluding,
									flagConsumptionTaxGeneralRuleProration : flagConsumptionTaxGeneralRuleProration,
									flagConsumptionTaxGeneralRuleEach      : flagConsumptionTaxGeneralRuleEach,
									flagConsumptionTaxSimpleRule           : flagConsumptionTaxSimpleRule,
									flagConsumptionTaxWithoutCalc          : flagConsumptionTaxWithoutCalc,
									flagConsumptionTaxCalc                 : flagConsumptionTaxCalc
								};
								obj.arr[i][str] = data;
								if ((obj.arr[i][str].numValue == '' || obj.arr[i][str].numValue == 0) && numNetTarget) {
									obj.arr[i][str].numValue = numNetTarget;
								}
								this._checkContraNumValue({
									vars    : obj.vars,
									varsArr : obj.arr[i]
								});
								break;
							}
						}

						if (obj.vars.value.match(/^=(.*?)$/)) {
							obj.vars.value = RegExp.$1;
						}

						if (parseFloat(this.flagIn) == 1) {
							if (!obj.vars.flagDebit) {
								this._setVarsRecentIdAccountTitle({idAccountTitle : obj.vars.value});
							}

						} else if (parseFloat(this.flagIn) == 0) {
							if (obj.vars.flagDebit) {
								this._setVarsRecentIdAccountTitle({idAccountTitle : obj.vars.value});
							}
						}

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
	_getEditVars : function(obj)
	{
		var varsStyle = this.allot({
			insCurrent : this.insCurrent,
			from       : '_getEditVars'
		});

		if (obj.vars.flagKey == 'idAccountTitle') {
			if (parseFloat(this.flagIn) == 1) {
				if (obj.vars.flagDebit) {
					obj.vars.arrayOption = this.vars.varsRule.arrAccountTitleCash.arrSelectTag;
				}

			} else if (parseFloat(this.flagIn) == 2) {
				obj.vars.arrayOption = this.vars.varsRule.arrAccountTitleCash.arrSelectTag;

			} else {
				if (!obj.vars.flagDebit) {
					obj.vars.arrayOption = this.vars.varsRule.arrAccountTitleCash.arrSelectTag;
				}
			}
		}
		var varsFormTemp = (Object.toJSON(this.vars.varsTmpl.varsFormTemp)).evalJSON();
		varsFormTemp.varsStatus.numTop = obj.ele.offsetTop - varsStyle.numTop;
		varsFormTemp.varsStatus.numLeft = obj.ele.offsetLeft - varsStyle.numLeft;

		varsFormTemp.varsDetail = obj.vars;
		varsFormTemp.varsDetail.numWidth = obj.ele.offsetWidth;
		varsFormTemp.varsDetail.numHeight = obj.ele.offsetHeight;

		return varsFormTemp;
	}
});





<?php }
}
?>