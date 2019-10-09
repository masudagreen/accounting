<?php /* Smarty version 3.1.24, created on 2019-06-16 09:02:21
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/plugin/accounting/js/jpn/portal.js" */ ?>
<?php
/*%%SmartyHeaderCode:3512888235d06059d6f7885_69061716%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '97dea9513ef4aaea505d276d3ac2cd9b3ed88fa4' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/plugin/accounting/js/jpn/portal.js',
      1 => 1560675145,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '3512888235d06059d6f7885_69061716',
  'variables' => 
  array (
    'varsLoad' => 0,
    'numNews' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_5d06059d6ffc82_47936278',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_5d06059d6ffc82_47936278')) {
function content_5d06059d6ffc82_47936278 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '3512888235d06059d6f7885_69061716';
?>

/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Plugin_Accounting_Portal = Class.create(Code_Lib_ExtPreference,
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
		this._iniDetail();
/*
		this._iniChild({
			strTitleParent : this.insWindow.vars.strTitle,
			strTitleChild  : '',
			strExt         : 'BlueSheet',
			strClass       : 'Plugin',
			strChild       : '',
			idModule       : 'Accounting'
		});

*/
	},

	/**
	 *
	*/
	_iniVars : function(obj)
	{
		this._extVars(obj);
		this.insTop = this;
		this.insRoot.vars.varsSystem.token = this.vars.token;
		this.strClass = this.vars.strClass;
		this.idModule = this.vars.idModule;
		this.insWindow.addStrWindowTitle({strTitle : this.vars.strTitle});
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
				insCurrent.insNavi.eventLayout();
				insCurrent.insDetail.eventLayout();

			} else if (obj.from == 'preEventLayout') {
				insCurrent._preEventLayout({flag : 'dummy'});

			} else if (obj.from == 'navi-_mousedownNavi') {
				if (obj.vars.id == 'Reload') {
					insCurrent._preEventLayout({flag : 'reset'});
					insCurrent.insLayout.updateTool({flagLock : 1, from : 'navi', idTarget : obj.vars.id});
					insCurrent._sendNaviConnect();
				}

			} else if (obj.from == 'detail-_mousedownNavi') {
				if (obj.vars.id == 'Reload') {
					insCurrent._preEventLayout({flag : 'reset'});
					insCurrent.insLayout.updateTool({flagLock : 1, from : 'detail', idTarget : obj.vars.id});
					insCurrent._eventDetailConnect({flag : 'reload', idTarget : insCurrent.insDetail.varsEventNavi.vars.vars.idTarget});
				}
			}
		};

		return allot;
	},


	/**
	 *
	*/
	_iniNavi : function()
	{
		this._extNavi();
	},

	/**
	 *
	*/
	_getNaviAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;
			if (obj.from == 'tree-_mousedownBtn') insCurrent._eventNaviDetail({vars : obj.vars});
		};

		return allot;
	},

	/**
		{
		 idTarget    : str,
		 insBack     : ins,
		 strBackFunc : str
		}
	*/
	iniAutoBoot : function(obj)
	{
		this._varsBlock = null;
		this._getBlock({arr : this.vars.portal.varsNavi.tree.varsDetail.varsDetail, idTarget : obj.idTarget});
		if (this._varsBlock) {
			this._eventNaviDetail({
				vars           : this._varsBlock,
				flagHideWindow : (obj.flagHideWindow)? 1 : 0,
				insBack        : (obj.strBackFunc)? obj.insBack : {},
				strBackFunc    : (obj.strBackFunc)? obj.strBackFunc : ''
			});
		}
	},

	/**

	*/
	_flagAutoSearchOver : '',
	bootAutoSearchOver : function(obj)
	{
		var insEscape = new Code_Lib_Escape();
		this._flagAutoSearchOver = obj.flag;
/*
		if (obj.flag == 'flagIdAccountTitle') {
			this._varsBlock = null;
			this._getBlock({arr : this.vars.portal.varsNavi.tree.varsDetail.varsDetail, idTarget : obj.flag});
			if (this._varsBlock) {
				this._setNaviDetail({vars : this._varsBlock});
			}
		}
*/
	},

	/**
		{
		 strTarget : str,
		}
	*/
	_varsbootWindowTag : {},
	bootWindowTag : function(obj)
	{
		var insEscape = new Code_Lib_Escape();
		if (!obj.strTarget.match(/^(log|cash|fixedAssets|file|banks):(.*?)$/)) {
			return;
		}
		var idWindow = RegExp.$1;
		var idWindowCap = insEscape.strCapitalize({data : idWindow});
		var idLog = parseFloat((RegExp.$2));
		if (isNaN(idLog)) {
			return;
		}

		if (!parseFloat(this.vars['flagAuthority' + idWindowCap])) {
			alert(this.insRoot.vars.varsSystem.str.strAuthorityNone);
			return 1;
		}

		var vars = {};
		vars.idTarget = 'id';
		vars.id = idLog;

		this._varsbootWindowTag = {};
		this._varsbootWindowTag.flag = idWindowCap;
		this._varsbootWindowTag.vars = vars;
		var varsData = this.insTop.checkChildData({idTarget : idWindowCap});
		if (!varsData) {
			var idTarget = insEscape.strLowCapitalize({data : idWindowCap});
			this.insTop.iniAutoBoot({
				idTarget     : idTarget + 'Window',
				insBack      : this,
				strBackFunc  : 'eventWindowTag'
			});

		} else {
			if (varsData.insWindow.vars.flagHideNow) {
				varsData.insWindow.updateHide({ flagEffect : 1 });
			} else {
				varsData.insWindow.setZIndex();
			}
			this.eventWindowTag();
		}
		return 1;

	},

	eventWindowTag : function()
	{
		var varsData = this.insTop.checkChildData({idTarget : this._varsbootWindowTag.flag});
		if (varsData) {
			varsData.insClass.bootAutoSearchOver({
				flag : 'show' + this._varsbootWindowTag.flag,
				vars : this._varsbootWindowTag.vars,
			});
		}
	},

	/**
	 *
	*/
	_eventNaviDetail : function(obj)
	{
		if (obj.vars.vars.idTarget.match(/(.*?)Window$/)) {
			var insEscape = new Code_Lib_Escape();
			var strExt = insEscape.strCapitalize({data : RegExp.$1});
			this._iniChild({
				strTitleParent : this.insWindow.vars.strTitle,
				strTitleChild  : obj.vars.strTitle,
				varsCall       : obj.vars,
				strExt         : strExt,
				strChild       : '',
				strClass       : this.strClass,
				idModule       : this.idModule,
				flagHideWindow : (obj.flagHideWindow)? 1 : 0,
				insBack        : (obj.strBackFunc)? obj.insBack : {},
				strBackFunc    : (obj.strBackFunc)? obj.strBackFunc : ''
			});

		} else {
			this._updateNaviDetailVars({vars : obj.vars});
			this._setNaviDetail({vars : obj.vars});
		}
	},

	/**
	 *
	*/
	_updateNaviDetailVars : function(obj)
	{


	},

	/**
	 *
	*/
	_setDetailStart : function()
	{

		this._varsBlock = null;
		this._getBlock({arr : this.vars.portal.varsNavi.tree.varsDetail.varsDetail, idTarget : 'userBoard'});
		if (this._varsBlock) {
			this._setNaviDetail({vars : this._varsBlock});

		} else {
			this.insDetail.eventList({
				strTitle    : this.vars.portal.varsDetail.varsStart.strTitle,
				strClass    : this.vars.portal.varsDetail.varsStart.strClass,
				vars        : {
					varsDetail : this.vars.portal.varsDetail.varsStart.varsDetail,
					varsBtn    : null,
					varsEdit   : {},
					vars       : {}
				}
			});
		}
	},

	/**
	 *
	*/
	_setNaviDetail : function(obj)
	{
		var objDetail = obj.vars.vars.varsDetail;
		this.insDetail.eventNavi({
			strTitle : obj.vars.strTitle,
			strClass : obj.vars.strClass,
			vars     : {
				varsDetail : objDetail,
				varsEdit   : obj.vars.vars.varsEdit,
				varsBtn    : obj.vars.vars.varsBtn,
				vars       : obj.vars
			}
		});
		this._setDetailContent({idTarget : obj.vars.vars.idTarget, vars : obj.vars});
	},

	/**
	 *
	*/
	_varsContent : {num : 0},
	_setDetailContent : function(obj)
	{
		this._varsContent.num = 0;
		if (obj.idTarget == 'idEntityCurrent'
			|| obj.idTarget == 'flagCR'
			|| obj.idTarget == 'jsonFileType'
		) {
			this._iniDetailFormList();

		} else if (obj.idTarget == 'charge') {
			this._iniDetailFormCheck();

		} else if (obj.idTarget == 'userBoard') {
			this._iniDetailBoard({vars : obj.vars});
		}
	},

	/**
	 *
	*/

	_varsDetailBoard : {},
	_iniDetailBoard : function(obj)
	{
		this._varsDetailBoard = {};
		this._setDetailBoard({arr : this.insDetail.insForm.vars.varsDetail, vars : obj.vars});
	},

	/**
	 *
	*/
	_setDetailBoard : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].varsBoard) continue;
			var ele = this.insDetail.insForm.eleWrap.down('.codeLibFormContent', this._varsContent.num);
			this._varsContent.num++;
			var insBoard = new Code_Plugin_Accounting_Lib_Board({
				eleScroll  : this.insDetail.insForm.eleInsert,
				eleInsert  : ele,
				insRoot    : this.insRoot,
				insCurrent : this,
				idSelf     : this.idSelf + 'Board' + obj.arr[i].id,
				allot      : this._getDetailBoardAllot(),
				vars       : obj.arr[i].varsBoard
			});
			this._varsDetailBoard[obj.arr[i].id] = insBoard;
			break;
		}
	},

	/**
	 *
	*/
	_preEventLayoutDetailBoard : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].varsBoard) continue;
			this._varsDetailBoard[obj.arr[i].id].stopListener();
			break;
		}
	},

	/**
	 *
	*/
	_getDetailBoardAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;
			if (obj.from == '_checkTextBtn') {
				insCurrent._eventDetailBoardTextBtn(obj);
			}

		};

		return allot;
	},

	/**
	 *
	*/
	_eventDetailBoardTextBtn : function(obj)
	{
		this._checkAutoSearch({idTarget : obj.vars.vars.idTarget});
	},

	/**
	 *
	*/
	_flagAutoSearch : '',
	_checkAutoSearch : function(obj)
	{
		var insEscape = new Code_Lib_Escape();
		this._flagAutoSearch = obj.idTarget;
		var idTarget = obj.idTarget;
		if (this._flagAutoSearch == 'LogImportRetry'
			|| this._flagAutoSearch == 'LogImport'
		) {
			idTarget = 'Log';

		} else if (this._flagAutoSearch == 'CashDefer') {
			idTarget = 'Cash';

		} else if (this._flagAutoSearch == 'BanksAccount') {
			idTarget = 'Banks';
		}

		var varsData = this.insTop.checkChildData({idTarget : idTarget});
		if (!varsData) {
			var idTarget = insEscape.strLowCapitalize({data : idTarget});
			this.insTop.iniAutoBoot({
				idTarget     : idTarget + 'Window',
				insBack      : this,
				strBackFunc  : 'eventAutoSearch'
			});

		} else {
			if (!(this._flagAutoSearch == 'LogImportRetry'
				|| this._flagAutoSearch == 'LogImport'
				|| this._flagAutoSearch == 'CashDefer'
				|| this._flagAutoSearch == 'BanksAccount'
			)) {
				if (varsData.insWindow.vars.flagHideNow) {
					varsData.insWindow.updateHide({ flagEffect : 1 });

				} else {
					varsData.insWindow.setZIndex();
				}
			}
			this.eventAutoSearch({flag : 'showLog', varsData : varsData});
		}
	},

	eventAutoSearch : function(obj)
	{
		if (this._flagAutoSearch == 'LogImportRetry'
			|| this._flagAutoSearch == 'LogImport'
		) {
			var varsData = this.insTop.checkChildData({idTarget : 'Log'});
			if (varsData) {
				varsData.insClass.bootAutoSearchOver({flag : 'show' + this._flagAutoSearch});
			}

		} else if (this._flagAutoSearch == 'CashDefer') {
			var varsData = this.insTop.checkChildData({idTarget : 'Cash'});
			if (varsData) {
				varsData.insClass.bootAutoSearchOver({flag : 'show' + this._flagAutoSearch});
			}

		} else if (this._flagAutoSearch == 'BanksAccount') {
			var varsData = this.insTop.checkChildData({idTarget : 'Banks'});
			if (varsData) {
				varsData.insClass.bootAutoSearchOver({flag : 'show' + this._flagAutoSearch});
			}

		} else {
			if (obj.flag) {
				obj.varsData.insClass.bootAutoSearchOver({flag : obj.flag});
			}
		}
	},

	/**
	 *
	*/
	_iniDetailFormList : function()
	{
		this._extDetailFormList();
	},

	/**
	 *
	*/
	_getDetailFormListAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;
			var insParent = insCurrent.insCurrent;
			if (obj.from == '_mousedownAdd') {

				obj.arr = insParent.insDetail.insForm.vars.varsDetail;
				var num = 0;
				for (var i = 0; i < obj.arr.length; i++) {
					if (!obj.arr[i].varsFormList) continue;
					if (obj.arr[i].id == 'JsonFileType') {
						return;

					} else if (insCurrent.idSelf == insParent.insDetail.insForm.idSelf + 'DetailFormList' + obj.arr[i].id) {
						if (obj.arr[i].id == 'IdEntityCurrent' || obj.arr[i].id == 'IdEntityAccount') {
							var idTarget = 'PluginAccountingEntity';
							if (obj.arr[i].id == 'IdEntityAccount') {
								idTarget = 'PluginAccountingEntityWithoutConfig';
							}
							insParent.insRoot.insChoice.setBoot({
								flagId       : obj.arr[i].id,
								idTarget     : idTarget,
								idModule     : 'Accounting',
								flagCheckUse : 0,
								strFunc      : 'setDetailFormListChoiceValue',
								numTop       : insParent._staticDetailFormList.numTop + $(insParent.insWindow.idWindow).offsetTop,
								numLeft      : insParent._staticDetailFormList.numLeft + $(insParent.insWindow.idWindow).offsetLeft,
								insCurrent   : insParent
							});
						}
						break;
					}
					num++;
				}

				return 1;
			}
		};

		return allot;
	},

	/**
	 *
	*/
	_setDetailFormListChoiceValue : '',
	setDetailFormListChoiceValue : function(obj)
	{
		if (!obj.vars) return;
		this.insDetail.setValue();
		obj.arr = this.insDetail.insForm.vars.varsDetail;
		if (obj.flagId == 'IdEntityCurrent') {
			obj.arr = this._updateDetailFormListChoiceIdEntityCurrent(obj);
		}
		this._varsDetailFormListChoiceValue = '';
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].varsFormList) continue;
			if (obj.arr[i].id == obj.flagId) {
				var data = (Object.toJSON(obj.arr[i].varsFormList.templateDetail)).evalJSON();
				data.value = obj.vars.strTitle;
				this._setDetailFormListChoiceValue = obj.vars.strTitle;
				obj.arr[i].varsFormList.varsDetail[0] = data;
				obj.arr[i].value = obj.vars.vars.idTarget;
				this.insDetail.varsEventNavi.vars.vars.varsDetail = obj.arr;
				this._setNaviDetail({vars : this.insDetail.varsEventNavi.vars});
				return;
			}
		}
	},

	/**
	 *
	*/
	_varsCurrent : {
		flagNotMove : 0,
	},
	_updateDetailFormListChoiceIdEntityCurrent : function(obj)
	{

		this._varsCurrent.flagNotMove = 0;
		var tmplPeriod = (Object.toJSON(obj.arr[0].varsTmplDetail.numFiscalPeriodCurrent)).evalJSON();
		var numStart = parseFloat(obj.vars.numFiscalPeriodStart);
		var numLock = parseFloat(obj.vars.numFiscalPeriodLock);
		var numCurrent = parseFloat(obj.vars.numFiscalPeriodCurrent);
		var flagEntityCurrent = parseFloat(obj.vars.flagEntityCurrent);
		var numEnd = parseFloat(obj.vars.numFiscalPeriod);
		var numEndPrev = numEnd - 1;
		var numNet = numEnd - numLock;
		var numSelect = 0;
		for (var i = numEnd; i >= numStart ; i--) {
			numSelect++;
			var varsOption = {};
			var str = (Object.toJSON(tmplPeriod.varsTmpl)).evalJSON();
			var strTitle = str.replace(/<?php echo '<%'; ?>
strTitle<?php echo '%>'; ?>
/, i);
			if (numNet == 2) {
				if (numEnd == i) {
					strTitle += ' ' + tmplPeriod.varsStr.strUnLock;

				} else if (numEndPrev == i) {
					strTitle += ' ' + tmplPeriod.varsStr.strTempLock;

				} else {
					strTitle += ' ' + tmplPeriod.varsStr.strLock;
				}

			} else {
				if (numEnd == i) {
					strTitle += ' ' + tmplPeriod.varsStr.strUnLock;

				} else {
					strTitle += ' ' + tmplPeriod.varsStr.strLock;
				}
			}

			if (flagEntityCurrent) {
				if (numCurrent == i) {
					strTitle += ' ' + tmplPeriod.varsStr.strCurrent;
					varsOption.flagDisabled = 1;
					numSelect--;
				}
			}
			varsOption.strTitle = strTitle;
			varsOption.value = i;
			tmplPeriod.arrayOption.push(varsOption);
		}
		if (numSelect == 1 && tmplPeriod.arrayOption.length == 1) {
			tmplPeriod.value = tmplPeriod.arrayOption[0].value;
		}
		if (flagEntityCurrent && tmplPeriod.arrayOption.length == 1) {
			this._varsCurrent.flagNotMove = 1;
		}
		var array = [];
		array.push(obj.arr[0]);
		array.push(tmplPeriod);

		return array;
	},

	/**
	 *
	*/
	_iniDetailFormCheck : function()
	{
		this._extDetailFormCheck();
	},

	/**
	 *
	*/
	_getDetailFormCheckAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;
			insParent = insCurrent.insCurrent;
			if (obj.from == '_mousedownBtn') {
				obj.arr = insParent.insDetail.insForm.vars.varsDetail;
				for (var i = 0; i < obj.arr.length; i++) {
					if (!obj.arr[i].varsFormCheck) continue;
					insParent.insRoot.insChoice.setBoot({
						flagId       : {flagId : obj.arr[i].id, varsCheck : obj.vars},
						varsValue    : insCurrent._varsValue,
						idTarget     : obj.arr[i].varsFormCheck.varsChoice.idTarget,
						idModule     : obj.arr[i].varsFormCheck.varsChoice.idModule,
						flagCheckUse : obj.arr[i].varsFormCheck.varsChoice.flagCheckUse,
						strFunc      : 'setDetailFormCheckChoiceValue',
						numTop       : insParent._staticDetailFormList.numTop + $(insParent.insWindow.idWindow).offsetTop,
						numLeft      : insParent._staticDetailFormList.numLeft + $(insParent.insWindow.idWindow).offsetLeft,
						insCurrent   : insParent
					});

					break;
				}
			}
		};

		return allot;
	},


	/**
	 *
	*/
	setDetailFormCheckChoiceValue : function(obj)
	{
		this._setDetailFormCheckChoiceValue({
			idTarget  : obj.flagId.flagId,
			varsCheck : obj.flagId.varsCheck,
			vars      : obj.vars,
			arr       : this.insDetail.insForm.vars.varsDetail
		});
	},

	/**
	 *
	*/
	_setDetailFormCheckChoiceValue : function(obj)
	{
		var num = 0;
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].varsFormCheck) continue;
			if (obj.idTarget == obj.arr[i].id) {

				var varsDetail = this._setDetailFormCheckChoiceValueChild({
					insFormCheck : this._varsDetailFormCheck[num].insFormCheck,
					vars         : obj.vars,
					varsCheck    : obj.varsCheck,
					strBlank     : obj.arr[i].varsFormCheck.varsTmpl.strBlank,
					arr          : this._varsDetailFormCheck[num].insFormCheck.vars.varsDetail
				});
				this._varsDetailFormCheck[num].insFormCheck.vars.varsDetail = varsDetail;
				this._varsDetailFormCheck[num].insFormCheck.iniReload();

			}
			num++;
		}
	},

	/**
	 *
	*/
	_setDetailFormCheckChoiceValueChild : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == obj.varsCheck.vars.id) {
				if (obj.vars) {
					obj.arr[i].idAccount = obj.vars.vars.idTarget;
					obj.arr[i].varsColumnDetail.strAccount = obj.vars.strTitle;

				} else {
					obj.arr[i].idAccount = 0;
					obj.arr[i].varsColumnDetail.strAccount = obj.strBlank;
				}

				return obj.arr;
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
			else if (obj.from == 'form-preEventLayout') insCurrent._preEventLayoutDetailContent();
			else if (obj.from == 'form-eventLayout') insCurrent._eventLayoutDetailContent();
			else if (obj.from == 'form-eventBtnBottom') {
				if (obj.vars.vars.vars.flagBack) {
					insCurrent._backDetailEnd();

				} else if (obj.vars.vars.vars.flagOrder) {
					insCurrent._eventDetailOrder({idTarget : obj.vars.vars.vars.idTarget, id : obj.vars.vars.id});

				} else if (obj.vars.vars.vars.flagHide) {
					if (!insCurrent.insWindow.vars.flagHideNow) insCurrent.insWindow.updateHide({ flagEffect : 1 });

				} else {
					insCurrent._eventDetailConnect({
						flag     : 'edit',
						idTarget : obj.vars.vars.vars.idTarget,
						id       : obj.vars.vars.id
					});

				}
			}
		};

		return allot;
	},

	/**
	 *
	*/
	_varsDetailOrder : {},
	_eventDetailOrder : function(obj)
	{
		this.insDetail.setFormValueAll();

		var idTarget = '';
		if (obj.idTarget == 'flagCorporation') {
			this._resetDetailOrderVars();
			var vars = this.insDetail.getFormValue();
			this._varsDetailOrder[obj.id] = parseFloat(vars[obj.id]);
			idTarget = 'numFiscalBeginningYear';

		} else if (obj.idTarget == 'flagCorporationBack') {
			idTarget = 'flagCorporation';

		} else if (obj.idTarget == 'numFiscalBeginningYear') {
			var vars = this.insDetail.getFormValue();
			this._varsDetailOrder[obj.id] = parseFloat(vars[obj.id]);
			if (parseFloat(this._varsDetailOrder.FlagCorporation) == 1) {
				idTarget = 'numFiscalBeginningMonth';

			} else {
				this._varsDetailOrder.NumFiscalBeginningMonth = 1;
				idTarget = 'flagCR';
			}

		} else if (obj.idTarget == 'numFiscalBeginningYearBack') {
			idTarget = 'numFiscalBeginningYear';

		} else if (obj.idTarget == 'numFiscalBeginningMonth') {
			var vars = this.insDetail.getFormValue();
			this._varsDetailOrder[obj.id] = parseFloat(vars[obj.id]);
			idTarget = 'flagCR';

		} else if (obj.idTarget == 'numFiscalBeginningMonthBack') {
			if (parseFloat(this._varsDetailOrder.FlagCorporation) == 1) {
				idTarget = 'numFiscalBeginningMonth';

			} else {
				idTarget = 'numFiscalBeginningYear';
			}

		} else if (obj.idTarget == 'flagCR') {
			var vars = this.insDetail.getFormValue();
			this._varsDetailOrder[obj.id] = parseFloat(vars[obj.id]);
			var id = parseFloat(vars.IdEntityAccount);
			this._varsDetailOrder.IdEntityAccount = (id)? id : '';
			idTarget = 'flagConsumptionTaxFree';


		} else if (obj.idTarget == 'flagCRBack') {
			idTarget = 'flagCR';

		} else if (obj.idTarget == 'flagConsumptionTaxFree') {
			var vars = this.insDetail.getFormValue();
			this._varsDetailOrder[obj.id] = parseFloat(vars[obj.id]);
			if (this._varsDetailOrder[obj.id]) {
				idTarget = 'entityConfigEnd';
				this._varsDetailOrder.FlagConsumptionTaxIncluding = 1;

			} else idTarget = 'flagConsumptionTaxGeneralRule';


		} else if (obj.idTarget == 'flagConsumptionTaxFreeBack') {
			idTarget = 'flagConsumptionTaxFree';

		} else if (obj.idTarget == 'flagConsumptionTaxGeneralRule') {
			var vars = this.insDetail.getFormValue();
			this._varsDetailOrder[obj.id] = parseFloat(vars[obj.id]);
			if (this._varsDetailOrder[obj.id]) idTarget = 'flagConsumptionTaxDeducted';
			else idTarget = 'flagConsumptionTaxBusinessType';


		} else if (obj.idTarget == 'flagConsumptionTaxGeneralRuleBack') {
			idTarget = 'flagConsumptionTaxGeneralRule';

		} else if (obj.idTarget == 'flagConsumptionTaxDeducted') {
			var vars = this.insDetail.getFormValue();
			this._varsDetailOrder[obj.id] = parseFloat(vars[obj.id]);
			idTarget = 'flagConsumptionTaxIncluding';

		} else if (obj.idTarget == 'flagConsumptionTaxBusinessType') {
			var vars = this.insDetail.getFormValue();
			this._varsDetailOrder[obj.id] = parseFloat(vars[obj.id]);
			idTarget = 'flagConsumptionTaxIncluding';


		} else if (obj.idTarget == 'flagConsumptionTaxDeductedBack') {
			if (this._varsDetailOrder.FlagConsumptionTaxFree) idTarget = 'flagConsumptionTaxFree';
			else if (this._varsDetailOrder.FlagConsumptionTaxGeneralRule) idTarget = 'flagConsumptionTaxDeducted';
			else idTarget = 'flagConsumptionTaxBusinessType';

		} else if (obj.idTarget == 'flagConsumptionTaxIncluding') {
			var vars = this.insDetail.getFormValue();
			this._varsDetailOrder[obj.id] = parseFloat(vars[obj.id]);
			this._varsDetailOrder.FlagConsumptionTaxCalc = parseFloat(vars.FlagConsumptionTaxCalc);
			if (this._varsDetailOrder[obj.id]) idTarget = 'entityConfigEnd';
			else idTarget = 'flagConsumptionTaxWithoutCalc';


		} else if (obj.idTarget == 'flagConsumptionTaxIncludingBack') {
			idTarget = 'flagConsumptionTaxIncluding';

		} else if (obj.idTarget == 'flagConsumptionTaxWithoutCalc') {
			var vars = this.insDetail.getFormValue();
			this._varsDetailOrder[obj.id] = parseFloat(vars[obj.id]);
			idTarget = 'entityConfigEnd';


		} else if (obj.idTarget == 'flagConsumptionTaxWithoutCalcBack') {
			if (this._varsDetailOrder.FlagConsumptionTaxFree) idTarget = 'flagConsumptionTaxFree';
			else if (this._varsDetailOrder.FlagConsumptionTaxIncluding) idTarget = 'flagConsumptionTaxIncluding';
			else if (!this._varsDetailOrder.FlagConsumptionTaxIncluding) idTarget = 'flagConsumptionTaxWithoutCalc';

		} else if (obj.idTarget == 'entityConfigEnd') {
			this._eventDetailConnect({
				flag     : 'order',
				vars     : this._varsDetailOrder,
				idTarget : obj.idTarget
			});
			return;
		}

		this._setDetailOrder({idTarget  : idTarget});
	},

	/**
	 *
	*/
	_setDetailOrderValue : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			var id = obj.arr[i].id;
			if (this._varsDetailOrder[id] != undefined) obj.arr[i].value = this._varsDetailOrder[id];
			if (id == 'IdEntityAccount') {
				var data = (Object.toJSON(obj.arr[i].varsFormList.templateDetail)).evalJSON();
				data.value = this._setDetailFormListChoiceValue;
				obj.arr[i].varsFormList.varsDetail[0] = data;

			} else if (id == 'numFiscalBeginningYear') {
				var objTime = this.insRoot.insTimeZone.adjustDate({stamp : new Date().getTime()});
				obj.arr[i].arrayOption = this._getDetailOrderNumFiscalBeginningYear({strYear : obj.arr[i].varsTmpl.strYear});
				obj.arr[i].numSize = obj.arr[i].arrayOption.length;
				if (obj.arr[i].value == 0) obj.arr[i].value = objTime.numYear;

			} else if (id == 'NumFiscalBeginningMonth') {
				var arr = (Object.toJSON(obj.arr[i].varsTmpl)).evalJSON();
				if (this._varsDetailOrder.numFiscalBeginningYear == 1997 && this._varsDetailOrder.FlagCorporation) {
					var arrayNew = [];
					for (var j = 0; j < arr.length; j++) {
						if (arr[j].value >= 4) {
							arrayNew.push(arr[j]);
						}
					}
					arr = arrayNew;
				}
				obj.arr[i].arrayOption = arr;

			} else if (id == 'EntityConfigEnd') {

				obj.arr[i].strComment += this._getDetailOrderValueStr({tmplTarget : 'flagCorporation', idTarget : 'FlagCorporation', varsTmpl : obj.arr[i].varsTmpl});

				obj.arr[i].strComment += this._getDetailOrderValueStr({tmplTarget : 'numFiscalBeginningYear', idTarget : 'numFiscalBeginningYear', varsTmpl : obj.arr[i].varsTmpl});

				obj.arr[i].strComment += this._getDetailOrderValueStr({tmplTarget : 'numFiscalBeginningMonth', idTarget : 'NumFiscalBeginningMonth', varsTmpl : obj.arr[i].varsTmpl});

				obj.arr[i].strComment += this._getDetailOrderValueStr({tmplTarget : 'flagCR', idTarget : 'FlagCR', varsTmpl : obj.arr[i].varsTmpl});
				obj.arr[i].strComment += this._getDetailOrderValueStrChoice({idTarget : 'IdEntityAccount', arr : this.insDetail.varsEventNavi.vars.varsTmpl.flagCR.varsDetail, varsTmpl : obj.arr[i].varsTmpl});

				obj.arr[i].strComment += this._getDetailOrderValueStr({tmplTarget : 'flagConsumptionTaxFree', idTarget : 'FlagConsumptionTaxFree', varsTmpl : obj.arr[i].varsTmpl});

				if (!this._varsDetailOrder.FlagConsumptionTaxFree) {
					obj.arr[i].strComment += this._getDetailOrderValueStr({tmplTarget : 'flagConsumptionTaxGeneralRule', idTarget : 'FlagConsumptionTaxGeneralRule', varsTmpl : obj.arr[i].varsTmpl});
					if (this._varsDetailOrder.FlagConsumptionTaxGeneralRule) {
						obj.arr[i].strComment += this._getDetailOrderValueStr({tmplTarget : 'flagConsumptionTaxDeducted', idTarget : 'FlagConsumptionTaxDeducted', varsTmpl : obj.arr[i].varsTmpl});

					} else {
						obj.arr[i].strComment += this._getDetailOrderValueStr({tmplTarget : 'flagConsumptionTaxBusinessType', idTarget : 'FlagConsumptionTaxBusinessType', varsTmpl : obj.arr[i].varsTmpl});

					}
					obj.arr[i].strComment += this._getDetailOrderValueStr({tmplTarget : 'flagConsumptionTaxIncluding', idTarget : 'FlagConsumptionTaxIncluding', varsTmpl : obj.arr[i].varsTmpl});
					obj.arr[i].strComment += this._getDetailOrderValueStr({tmplTarget : 'flagConsumptionTaxIncluding', idTarget : 'FlagConsumptionTaxCalc', varsTmpl : obj.arr[i].varsTmpl});

					if (!this._varsDetailOrder.FlagConsumptionTaxIncluding) {
						obj.arr[i].strComment += this._getDetailOrderValueStr({tmplTarget : 'flagConsumptionTaxWithoutCalc', idTarget : 'FlagConsumptionTaxWithoutCalc', varsTmpl : obj.arr[i].varsTmpl});
					}

				} else obj.arr[i].strComment += this._getDetailOrderValueStr({tmplTarget : 'flagConsumptionTaxIncluding', idTarget : 'FlagConsumptionTaxIncluding', varsTmpl : obj.arr[i].varsTmpl});

			}
		}
	},

	/**
	 *
	*/
	_getDetailOrderNumFiscalBeginningYear : function(obj)
	{
		var arrayNew = [];
		var objTime = this.insRoot.insTimeZone.adjustDate({stamp : new Date().getTime()});
		var numStart = 1998;
		var numEnd = objTime.numYear + 1;
		if (this._varsDetailOrder.FlagCorporation) {
			numStart = 1997;
		}
		for (var j = numStart; j <= numEnd; j++) {
			var objData = {
				strTitle : j + obj.strYear,
				value    : j
			};
			arrayNew.unshift(objData);
		}

		return arrayNew;
	},

	/**
	 *
	*/
	_getDetailOrderValueStrChoice : function(obj)
	{
		if (this._setDetailFormListChoiceValue == '') return '';

		var strTitle = '';
		var strValue = this._setDetailFormListChoiceValue;
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == obj.idTarget) {
				strTitle = obj.arr[i].strTitle;
				break;
			}
		}
		var str = (Object.toJSON(obj.varsTmpl)).evalJSON();
		str = str.replace(/<?php echo '<%'; ?>
strTitle<?php echo '%>'; ?>
/, strTitle);
		str = str.replace(/<?php echo '<%'; ?>
strValue<?php echo '%>'; ?>
/, strValue);

		return str;
	},

	/**
	 *
	*/
	_getDetailOrderValueStr : function(obj)
	{
		return this._getDetailOrderValueStrChild({
			arr         : this.insDetail.varsEventNavi.vars.varsTmpl[obj.tmplTarget].varsDetail,
			idTarget    : obj.idTarget,
			valueTarget : this._varsDetailOrder[obj.idTarget],
			varsTmpl    : (Object.toJSON(obj.varsTmpl)).evalJSON()
		});
	},

	/**
	 *
	*/
	_getDetailOrderValueStrChild : function(obj)
	{
		var strTitle = '';
		var strValue = '';
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == obj.idTarget) {
				strTitle = obj.arr[i].strTitle;
				var arr = obj.arr[i].arrayOption;
				if (obj.arr[i].id == 'numFiscalBeginningYear') {
					arr = this._getDetailOrderNumFiscalBeginningYear({strYear : obj.arr[i].varsTmpl.strYear});

				} else if (obj.arr[i].id == 'NumFiscalBeginningMonth') {
					arr = obj.arr[i].varsTmpl;
				}
				for (var j = 0; j < arr.length; j++) {
					if (arr[j].value == obj.valueTarget) {
						strValue = arr[j].strTitle;
						break;
					}
				}

				break;
			}
		}
		var str = (Object.toJSON(obj.varsTmpl)).evalJSON();
		str = str.replace(/<?php echo '<%'; ?>
strTitle<?php echo '%>'; ?>
/, strTitle);
		str = str.replace(/<?php echo '<%'; ?>
strValue<?php echo '%>'; ?>
/, strValue);

		return str;
	},

	/**
	 *
	*/
	_setDetailOrder : function(obj)
	{
		var vars = (Object.toJSON(this.insDetail.varsEventNavi.vars.varsTmpl[obj.idTarget])).evalJSON();
		this.insDetail.varsEventNavi.vars.vars = vars;
		this._setDetailOrderValue({arr : vars.varsDetail});
		this.insDetail.eventNavi({
			strTitle : this.insDetail.varsEventNavi.vars.strTitle,
			strClass : this.insDetail.varsEventNavi.vars.strClass,
			vars     : {
				varsDetail : vars.varsDetail,
				varsEdit   : vars.varsEdit,
				varsBtn    : vars.varsBtn,
				vars       : this.insDetail.varsEventNavi.vars
			}
		});
		this._setDetailContent({idTarget : obj.idTarget});
	},

	/**
	 *
	*/
	_resetDetailOrderVars : function()
	{
		this._setDetailFormListChoiceValue = '';
		this._varsDetailOrder = {};

	},

	/**
	 *
	*/
	_eventLayoutDetailContent : function()
	{
		if (!this.insDetail.varsEventNavi) return;
		if (!this.insDetail.varsEventNavi.vars.vars) return;
		var idTarget = this.insDetail.varsEventNavi.vars.vars.idTarget;
		if (idTarget == 'idEntityCurrent'
			|| idTarget == 'flagCR'
			|| idTarget == 'jsonFileType'
		) {
			this._iniDetailFormList();

		} else if (idTarget == 'arrCommaIdAccountMaintenance') {
			this._iniDetailFormArea();

		} else if (idTarget == 'charge') {
			this._iniDetailFormCheck();

		} else if (idTarget == 'userBoard') {
			this._varsBlock = null;
			this._getBlock({arr : this.insNavi.insTree.vars.varsDetail, idTarget : 'userBoard'});
			this._setNaviDetail({vars : this._varsBlock});
		}
	},

	/**
	 *
	*/
	_preEventLayoutDetailContent : function()
	{
		if (!this.insDetail.varsEventNavi) return;
		if (!this.insDetail.varsEventNavi.vars.vars) return;
		var idTarget = this.insDetail.varsEventNavi.vars.vars.idTarget;
		if (idTarget == 'idEntityCurrent'
			|| idTarget == 'flagCR'
			|| idTarget == 'jsonFileType'
		) {
			this._getDetailFormListVars({arr : this.insDetail.insForm.vars.varsDetail});

		} else if (idTarget == 'arrCommaIdAccountMaintenance') {
			this._getDetailFormAreaVars({arr : this.insDetail.insForm.vars.varsDetail});

		} else if (idTarget == 'charge') {
			this._getDetailFormCheckVars({arr : this.insDetail.insForm.vars.varsDetail});

		} else if (idTarget == 'nextData') {

		} else if (idTarget == 'userBoard') {

		}


	},

	/**
	 *
	*/
	_eventRemoveDetailContent : function()
	{
		if (!this.insDetail.varsEventNavi) return;
		if (!this.insDetail.varsEventNavi.vars.vars) return;
		var idTarget = this.insDetail.varsEventNavi.vars.vars.idTarget;
		if (idTarget == 'userBoard') {
			this._preEventLayoutDetailBoard({arr : this.insDetail.insForm.vars.varsDetail});
		}
	},

	/**
	 *
	*/
	_eventDetailConnect : function(obj)
	{
		if (obj.flag == 'reload') {
			this._eventValue({
				vars     : '',
				idTarget : obj.idTarget
			});

		} else if (obj.flag == 'edit') {
			if (obj.idTarget == 'arrCommaIdAccountMaintenance') {
				this._setDetailFormAreaValue({arr : this.insDetail.insForm.vars.varsDetail});

			} else if (obj.idTarget == 'jsonFileType') {
				this._setDetailFormListValue({arr : this.insDetail.insForm.vars.varsDetail});

			} else if (obj.idTarget == 'idEntityCurrent') {
				var vars = this.insDetail.getFormValue();
				if (this._varsCurrent.flagNotMove && !vars.NumFiscalPeriodCurrent) {
					this.insDetail.showFormAttestError({flagType : 'notMove'});
					return;
				}

			}
			if (this.insDetail.checkForm({flagType : 'common'})) return;
			var vars = this.insDetail.getFormValue();

			if (obj.idTarget == 'charge') {
				this._setDetailFormCheckValue({arr : this.insDetail.insForm.vars.varsDetail});
				vars = this.insDetail.getFormValue();
				obj.arr = vars.Charge;
				var data = {};

				for (var i = 0; i < obj.arr.length; i++) {
					data[obj.arr[i].id] = obj.arr[i].idAccount;
				}
				if (!obj.arr[0].idAccount || !obj.arr[1].idAccount) {
					this.insDetail.showFormAttestError({flagType : 'strBlank'});
					return;

				} else if (obj.arr[0].idAccount == obj.arr[1].idAccount) {
					this.insDetail.showFormAttestError({flagType : 'strSame'});
					return;
				}
				vars.Charge = data;

			}
			this._eventValue({
				vars     : vars,
				idTarget : obj.idTarget
			});

		} else if (obj.flag == 'order') {
			obj.flag = 'edit';
			this._eventValue({
				vars     : obj.vars,
				idTarget : obj.idTarget
			});

		}
		this._varsDetailConnect = obj;
		this._sendDetailConnect();
	},



	/**
	 *
	*/
	_eventDetailConnectSuccess : function(obj)
	{
		if (obj.json.flag == 1) {
			if (obj.json.data.idTarget == 'flagConsumptionTaxFree'
				|| obj.json.data.idTarget == 'flagConsumptionTaxGeneralRule'
				|| obj.json.data.idTarget == 'flagConsumptionTaxDeducted'
				|| obj.json.data.idTarget == 'flagConsumptionTaxBusinessType'
				|| obj.json.data.idTarget == 'flagConsumptionTaxIncluding'
				|| obj.json.data.idTarget == 'flagConsumptionTaxWithoutCalc'
			) {
				this.insNavi.updateTreeVars({vars : obj.json.data.vars});
				if (this._varsDetailConnect.flag == 'reload') {
					if (this.insDetail.varsEventNavi.vars.vars.idTarget == obj.json.data.idTarget) {
						this._setNaviDetail({vars : obj.json.data});
					}

				} else if (this._varsDetailConnect.flag == 'edit') {
					if (this.insDetail.varsEventNavi.vars.vars.idTarget == obj.json.data.idTarget) {
						this._setDetailEnd();
						this.insDetail.hideBtnBottom();
					}
					this.insRoot.iniPopup({flag : 'reload'});
					setTimeout(function() { location.href = this.insRoot.vars.varsSystem.path.post;}, 3000);
				}

			} else if (!obj.json.stamp.id.match(/DetailReload$/)
				&& (this.insDetail.varsEventNavi.vars.vars.idTarget == 'idEntityCurrent'
					|| this.insDetail.varsEventNavi.vars.vars.idTarget == 'nextData')
			) {
				if (this.insDetail.varsEventNavi.vars.vars.idTarget == obj.json.data.vars.idTarget) {
					this._setDetailEnd();
					this.insDetail.hideBtnBottom();
				}
				this.insRoot.iniPopup({flag : 'reload'});
				setTimeout(function() { location.href = this.insRoot.vars.varsSystem.path.post;}, 3000);

			} else if (obj.json.data.idTarget == 'entityConfigEnd') {
				if (this.insDetail.varsEventNavi.vars.vars.idTarget == obj.json.data.idTarget) {
					this._setDetailEnd();
					this.insDetail.hideBtnBottom();
				}
				this.insRoot.iniPopup({flag : 'reload'});
				setTimeout(function() { location.href = this.insRoot.vars.varsSystem.path.post;}, 3000);

			} else {
				if (this._varsDetailConnect.flag == 'reload') {
					if (this.insDetail.varsEventNavi.vars.vars.idTarget == obj.json.data.vars.idTarget) {
						this._setNaviDetail({vars : obj.json.data});
					}

				} else if (this._varsDetailConnect.flag == 'edit') {
					if (obj.json.data.vars.idTarget == 'flagIdAccountTitle' || obj.json.data.vars.idTarget == 'accessCode') {
						this._setDetailEnd();
						this.insDetail.hideBtnBottom();
						this.insRoot.iniPopup({flag : 'reload'});
						setTimeout(function() { location.href = this.insRoot.vars.varsSystem.path.post;}, 3000);

					} else if (this.insDetail.varsEventNavi.vars.vars.idTarget == obj.json.data.vars.idTarget) {
						this._setNaviDetail({vars : obj.json.data});
						this._setDetailEnd();

					}
				}
				if (obj.json.stamp) {
					var data = (Object.toJSON(obj.json.data)).evalJSON();
					if (obj.json.stamp.id) this._varsStampCheck[obj.json.stamp.id] = data;
				}
				this.insNavi.updateTreeVarsDetail({vars : obj.json.data});
			}

		} else if (obj.json.flag == 4) {
			alert(this.insRoot.vars.varsSystem.str.maintenance);

		} else if (obj.json.flag == 8) {
			alert(this.insRoot.vars.varsSystem.str.oldData);

		} else if (obj.json.flag == 10) {
			this.insLayout.updateTool({flagLock : 0, from : 'detail', idTarget : 'Reload'});
			if (this.insDetail.varsEventNavi.vars.vars.idTarget == this._varsValue.idTarget) {
				if (obj.json.stamp) {
					this._setNaviDetail({vars : this._varsStampCheck[obj.json.stamp.id]});
				}
			}

		} else if (obj.json.flag == 40) {
			this.insLayout.updateTool({flagLock : 0, from : 'detail', idTarget : 'Reload'});
			if (this.insDetail.varsEventNavi.vars.vars.idTarget == this._varsValue.idTarget) {
				alert(this.insRoot.vars.varsSystem.str.oldData);
			}

		} else if (obj.json.flag == 'entityConfigEnd') {
			if (this.insDetail.varsEventNavi.vars.vars.idTarget == obj.json.data.idTarget) {
				this.insDetail.showFormAttestError({flagType : 'idEntity'});
			}

		} else if (obj.json.flag == 'textMaxOver') {
			this.insDetail.showFormAttestError({flagType : 'textMaxOver', str : obj.json.data.vars});

		} else if (obj.json.flag == 'nextData') {
			if (this.insDetail.varsEventNavi.vars.vars.idTarget == obj.json.data.idTarget) {
				if (obj.json.data.idAttest == 'log'
					|| obj.json.data.idAttest == 'logRetry'
					|| obj.json.data.idAttest == 'logFiexedAssets'
					|| obj.json.data.idAttest == 'logCashPay'
					|| obj.json.data.idAttest == 'logCashDefer'
				) {
					this.insDetail.showFormAttestError({flagType : obj.json.data.idAttest});

				} else {
					var str = this._getStrAttest({
						str      : (obj.json.data.str)? obj.json.data.str : '',
						idTarget : obj.json.data.idAttest,
						arr      : this.insDetail.insForm.vars.varsDetail
					});
					this.insDetail.showFormAttestError({
						flagType : obj.json.data.idAttest,
						str      : str
					});
				}

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
	_getStrAttest : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == 'NextData') {
				var str = obj.arr[i].varsTmpl[obj.idTarget];
				str = str.replace(/<?php echo '<%'; ?>
strTitle<?php echo '%>'; ?>
/, obj.str);

				return str;
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
	_iniCss : function()
	{
		this._extCss();
	}


});

<?php }
}
?>