<?php /* Smarty version 3.1.24, created on 2019-10-06 09:19:44
         compiled from "/app/rucaro/back/tpl/templates/else/core/base/js/account.js" */ ?>
<?php
/*%%SmartyHeaderCode:7449975665d99b1b0b3e144_18307095%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '6456e60a1b7465cc0b714a5ae4347a6e0255207d' => 
    array (
      0 => '/app/rucaro/back/tpl/templates/else/core/base/js/account.js',
      1 => 1570328739,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '7449975665d99b1b0b3e144_18307095',
  'variables' => 
  array (
    'varsLoad' => 0,
    'numNews' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_5d99b1b0c27d80_37736242',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_5d99b1b0c27d80_37736242')) {
function content_5d99b1b0c27d80_37736242 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '7449975665d99b1b0b3e144_18307095';
?>

/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Core_Base_Account = Class.create(Code_Lib_ExtPortal,
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
		this._iniList();
		this._iniDetail();
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
					insCurrent.insNavi.eventTool({
						idTarget : insCurrent.insNavi.vars.varsStatus.flagNow,
						flagLoop : 1
					});

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
					insCurrent.insList.eventTool({
						idTarget : insCurrent.insList.vars.varsStatus.flagNow,
						flagLoop : 1
					});

				} else if (obj.vars.id == 'Reload') {
					insCurrent._eventListConnect({flag : obj.vars.id, flagType : insCurrent.insList.vars.varsStatus.flagReloadNow});

				} else if (obj.vars.id == 'Output') {
					insCurrent._eventListConnect({flag : obj.vars.id, flagType : insCurrent.insList.vars.varsStatus.flagOutputNow});

				} else if (obj.vars.id == 'Print') {
					insCurrent._eventListConnect({flag : obj.vars.id, flagType : insCurrent.insList.vars.varsStatus.flagPrintNow});

				} else if (obj.vars.id == 'Search') {
					insCurrent._iniChild({
						strTitleParent : insCurrent.insWindow.vars.strTitle,
						strTitleChild  : insCurrent.vars.child.varsTitle[obj.vars.id],
						strExt         : insCurrent.strExt,
						strChild       : obj.vars.id,
						strClass       : insCurrent.strClass,
						idModule       : insCurrent.idModule
					});
				}

			} else if (obj.from == 'list-_mousedownMenu') {
				if (obj.vars.id == 'Switch') {
					return insCurrent.insList.vars.varsStatus.flagNow;

				} else if (obj.vars.id == 'Output') {
					return insCurrent.insList.vars.varsStatus.flagOutputNow;

				} else if (obj.vars.id == 'Print') {
					return insCurrent.insList.vars.varsStatus.flagPrintNow;

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

			} else if (obj.from == 'detail-_mousedownNavi') {

				if (obj.vars.id == 'Reload') {
					insCurrent._preEventLayout({flag : 'reset'});
					insCurrent._eventDetailConnect({flag : 'reload'});

				} else if (obj.vars.id == 'Output') {
					insCurrent._eventDetailConnect({flag : 'output', flagType : insCurrent.insDetail.vars.varsStatus.flagOutputNow});

				} else if (obj.vars.id == 'Print') {
					insCurrent._eventDetailConnect({flag : obj.vars.id, flagType : insCurrent.insDetail.vars.varsStatus.flagPrintNow});

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

			} else if (obj.from == 'detail-_mousedownMenu') {
				if (obj.vars.id == 'Switch') {
					return insCurrent.insDetail.vars.varsStatus.flagNow;

				} else if (obj.vars.id == 'Output') {
					return insCurrent.insDetail.vars.varsStatus.flagOutputNow;

				} else if (obj.vars.id == 'Print') {
					return insCurrent.insDetail.vars.varsStatus.flagPrintNow;
				}

			} else if (obj.from == 'detail-_mousedownLine') {
				if (obj.varsTarget == 'Switch') {
					return insCurrent.insDetail.eventTool({idTarget : obj.vars});

				} else if (obj.varsTarget) {
					insCurrent.insDetail.eventTool({idTarget : obj.vars, flagStr : obj.varsTarget});
					if (obj.varsTarget == 'Output') {
						insCurrent._eventDetailConnect({flag : 'output', flagType : obj.vars});

					} else {
						insCurrent._eventDetailConnect({flag : obj.varsTarget, flagType : obj.vars});
					}
					return;
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
					arrWhere : this._varsSearch.ph.arrWhere,
					arrOrder : this._varsSearch.ph.arrOrder
				}
			});

		} else if (obj.flag == 'Reload') {
			if (obj.flagType == 'start') {
				this._resetSearch();
			}
			this._eventSearch({
				numLotNow : this._varsSearch.numLotNow,
				ph : {
					arrWhere : this._varsSearch.ph.arrWhere,
					arrOrder : this._varsSearch.ph.arrOrder
				}
			});

		} else if (obj.flag == 'Search') {
			this._eventSearch({
				numLotNow : obj.vars.numLotNow,
				ph : {
					arrWhere : this._varsSearch.ph.arrWhere,
					arrOrder : this._varsSearch.ph.arrOrder
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
		}

		this._varsListConnect = obj;
		this._sendListConnect();
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
			else if (obj.from == 'view-eventLayout') insCurrent._eventLayoutDetailContent();
			else if (obj.from == 'view-checkTextBtn') insCurrent._checkDetailContentTextBtn({vars : obj.vars});
			else if (obj.from == 'view-eventBtnBottom') insCurrent._eventDetailConnect({flag : obj.vars.vars.vars.idTarget});
		};

		return allot;
	},

	/**
	 *
	*/
	_checkDetailContentTextBtn : function(obj)
	{
		this.bootAutoSearch(obj);
	},

	/**
		{

		}
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
		var varsTmpl = {flagType: 'str', strColumn: '', flagCondition: 'eq', value: ''};

		var str = insEscape.strLowCapitalize({data : obj.vars.vars.idTarget});
		if (str == 'arrSpaceStrTag') {
			varsTmpl.flagType = 'tag';
			varsTmpl.flagCondition = 'like';
			varsTmpl.strColumn = str;
			varsTmpl.value = ' ' + obj.vars.vars.strTag + ' ';
			varsData.push(varsTmpl);

		} else if (str == 'strTerm') {
			varsTmpl.flagType = 'num';
			varsTmpl.strColumn = 'idTerm';
			varsTmpl.value = obj.vars.vars[str];
			varsData.push(varsTmpl);

		} else if (str == 'strModule') {
			varsTmpl.flagType = 'num';
			varsTmpl.strColumn = 'idModule';
			varsTmpl.value = obj.vars.vars[str];
			varsData.push(varsTmpl);

		} else if (str == 'id') {
			varsTmpl.flagType = 'num';
			varsTmpl.strColumn = str;
			varsTmpl.value = obj.vars.vars[str];
			varsData.push(varsTmpl);

		} else if (str == 'strCodeName' || str == 'idLogin' || str == 'strMailPc') {
			varsTmpl.strColumn = str;
			varsTmpl.value = obj.vars.vars[str];
			varsData.push(varsTmpl);
		}

		this._varsSearch.ph.arrWhere = varsData;
		this._eventListConnect({flag : flag});
	},


	/**
	 *
	*/
	_setDetailContent : function(obj)
	{

	},

	/**
	 *
	*/
	_eventRemoveDetailFormCheck : function(obj)
	{

	},

	/**
	 *
	*/
	_eventLayoutDetailContent : function()
	{

	},

	/**
	 *
	*/
	_preEventLayoutDetailContent : function()
	{

	},

	/**
	 *
	*/
	_eventRemoveDetailContent : function()
	{

	},

	/**
	 *
	*/
	_getDetailChildVars : function(obj)
	{
		var insDisplay = new Code_Lib_TimeDisplay();
		var arrayNew = [];
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == 'StrCodeName') {
				if (obj.flag == 'add') obj.arr[i].value = '';
				else if (obj.flag == 'copy') obj.arr[i].value = obj.vars.varsColumnDetail.strCodeName;
				else if (obj.flag == 'edit') obj.arr[i].value = obj.vars.varsColumnDetail.strCodeName;
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'StrMailPc') {
				if (obj.flag == 'add') obj.arr[i].value = '';
				else obj.arr[i].value = obj.vars.varsColumnDetail.strMailPc;
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'IdLogin') {
				if (obj.flag == 'add') obj.arr[i].value = '';
				else obj.arr[i].value = obj.vars.varsColumnDetail.idLogin;
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'ArrSpaceStrTag') {
				if (obj.flag == 'add') obj.arr[i].value = '';
				else obj.arr[i].value = obj.vars.arrSpaceStrTag;
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'IdTerm') {
				if (obj.flag == 'add') {
					obj.arr[i].varsFormList.varsDetail = [];
					obj.arr[i].value = '';
					arrayNew.push(obj.arr[i]);

				} else {
					obj.arr[i].value = obj.vars.idTerm;
					var data = (Object.toJSON(obj.arr[i].varsFormList.templateDetail)).evalJSON();
					data.value = obj.vars.varsColumnDetail.strTerm;
					obj.arr[i].varsFormList.varsDetail.push(data);
					if (!(obj.flag == 'edit' && obj.vars.varsColumnDetail.id == 1)) {
						arrayNew.push(obj.arr[i]);

					}
				}

			} else if (obj.arr[i].id == 'IdModule') {
				if (obj.flag == 'add') {
					obj.arr[i].value = '';
					obj.arr[i].varsFormList.varsDetail = [];
					arrayNew.push(obj.arr[i]);

				} else {
					obj.arr[i].value = obj.vars.idModule;
					var data = (Object.toJSON(obj.arr[i].varsFormList.templateDetail)).evalJSON();
					data.value = obj.vars.varsColumnDetail.strModule;
					obj.arr[i].varsFormList.varsDetail.push(data);
					if (!(obj.flag == 'edit' && obj.vars.varsColumnDetail.id == 1)) {
						arrayNew.push(obj.arr[i]);

					}
				}

			} else if (obj.arr[i].id == 'DummyWebmaster') {
				if (obj.flag == 'edit') {
					if (obj.vars.varsColumnDetail.id == 1) arrayNew.push(obj.arr[i]);
				}

			} else if (obj.arr[i].id == 'DummyPassword') {
				if (obj.flag != 'edit') {
					arrayNew.push(obj.arr[i]);
				}

			} else if (obj.arr[i].id == 'StrPassword') {
				obj.arr[i].value = '';
				if (obj.flag == 'edit') {
					arrayNew.push(obj.arr[i]);
				}

			} else if (obj.arr[i].id == 'StrPasswordConfirm') {
				obj.arr[i].value = '';
				if (obj.flag == 'edit') {
					arrayNew.push(obj.arr[i]);
				}

			}



		}

		return arrayNew;
	},


	/**
	 *
	*/
	_updateDetailListVars : function(obj)
	{
		var objDetail = (Object.toJSON(this.vars.portal.varsDetail.templateDetail)).evalJSON();
		var varsBtn = (Object.toJSON(this.vars.portal.varsDetail.varsBtn)).evalJSON();
		var objData = {
			flagMoveUse : 1,
			strTitle    : this.insDetail.vars.varsStart.strTitle,
			strClass    : obj.vars.strClass,
			vars        : {
				varsDetail : this._updateDetailListVarsChild({arr : objDetail, vars : obj.vars }),
				varsBtn    : this._updateDetailListVarsBtn({
					arr  : varsBtn,
					flag : obj.vars.flagDefault
				}),
				varsEdit   : this._updateDetailListVarsEdit({
					arr  : this.insDetail.vars.view.varsEdit,
					flag : 0
				}),
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
			var id = insEscape.strLowCapitalize({data : obj.arr[i].id});
			if (obj.arr[i].id == 'StampRegister') {
				var objTime = this.insRoot.insTimeZone.adjustDate({stamp : obj.vars.stampRegister * 1000});
				obj.arr[i].value = insDisplay.get({flagType : 1, vars : objTime});
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'StampUpdate') {
				var objTime = this.insRoot.insTimeZone.adjustDate({stamp : obj.vars.stampUpdate * 1000});
				obj.arr[i].value = insDisplay.get({flagType : 1, vars : objTime});
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'StrCodeName') {
				obj.arr[i].value = obj.vars.varsColumnDetail.strCodeName;
				var temp = {};
				temp.id = obj.arr[i].id;
				temp.strTitle = obj.arr[i].value;
				temp.vars = {};
				temp.vars[id] = obj.vars.vars[id];
				temp.vars.idTarget = obj.arr[i].id;
				obj.arr[i].varsTextBtn.push(temp);
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'StrMailPc') {
				obj.arr[i].value = obj.vars.varsColumnDetail.strMailPc;
				var temp = {};
				temp.id = obj.arr[i].id;
				temp.strTitle = obj.arr[i].value;
				temp.vars = {};
				temp.vars[id] = obj.vars.vars[id];
				temp.vars.idTarget = obj.arr[i].id;
				obj.arr[i].varsTextBtn.push(temp);
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'IdLogin') {
				obj.arr[i].value = obj.vars.varsColumnDetail.idLogin;
				var temp = {};
				temp.id = obj.arr[i].id;
				temp.strTitle = obj.arr[i].value;
				temp.vars = {};
				temp.vars[id] = obj.vars.vars[id];
				temp.vars.idTarget = obj.arr[i].id;
				obj.arr[i].varsTextBtn.push(temp);
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

			} else if (obj.arr[i].id == 'StrTerm' || obj.arr[i].id == 'StrModule') {
				obj.arr[i].value = (obj.vars.varsColumnDetail[id])? obj.vars.varsColumnDetail[id] : '-';
				if (obj.arr[i].value == '-') {
					obj.arr[i].varsTextBtn = null;
					arrayNew.push(obj.arr[i]);
					continue;
				}
				var temp = {};
				temp.id = obj.arr[i].id;
				temp.strTitle = obj.arr[i].value;
				temp.vars = {};
				temp.vars[id] = obj.vars.vars[id];
				temp.vars.idTarget = obj.arr[i].id;
				obj.arr[i].varsTextBtn.push(temp);
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