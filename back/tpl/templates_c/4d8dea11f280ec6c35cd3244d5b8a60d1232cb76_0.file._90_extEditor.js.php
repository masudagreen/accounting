<?php /* Smarty version 3.1.24, created on 2019-10-06 06:26:33
         compiled from "/app/rucaro/back/tpl/templates/else/core/base/js/lib/_90_extEditor.js" */ ?>
<?php
/*%%SmartyHeaderCode:19718090605d998919960873_07278007%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '4d8dea11f280ec6c35cd3244d5b8a60d1232cb76' => 
    array (
      0 => '/app/rucaro/back/tpl/templates/else/core/base/js/lib/_90_extEditor.js',
      1 => 1570328741,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '19718090605d998919960873_07278007',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_5d998919ba1a31_95948125',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_5d998919ba1a31_95948125')) {
function content_5d998919ba1a31_95948125 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '19718090605d998919960873_07278007';
?>

/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_ExtEditor = Class.create(Code_Lib_ExtPortal,
{

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
				if (insCurrent.insDetail) insCurrent.insDetail.eventLayout();

			} else if (obj.from == 'preEventLayout') {
				if (insCurrent.insNavi) insCurrent.insNavi.preEventLayout({flag : 'dummy'});
				if (insCurrent.insDetail) insCurrent.insDetail.preEventLayout({flag : 'dummy'});

			} else if (obj.from == 'navi-_mousedownNavi') {
				if (obj.vars.id == 'Switch') {
					insCurrent.insNavi.eventTool({
						idTarget : insCurrent.insNavi.vars.varsStatus.flagNow,
						flagLoop : 1
					});

				} else if (obj.vars.id == 'Reload') {
					if (insCurrent.insNavi.vars.varsStatus.flagNow.match(/^folder(.*?)$/)) {
						var flag = '';
						if (RegExp.$1) flag = 'format' + RegExp.$1 + '-reload';
						else flag = 'format-reload';
						insCurrent._eventNaviConnect({vars : obj.vars, flag : flag});
					}
				}

			} else if (obj.from == 'navi-_mousedownMenu') {
				return insCurrent.insNavi.vars.varsStatus.flagNow;

			} else if (obj.from == 'navi-_mousedownLine') {
				insCurrent.insNavi.eventTool({idTarget : obj.vars});

			} else if (obj.from == 'detail-_mousedownNavi') {
				var insEscape = new Code_Lib_Escape();
				var id = insEscape.strLowCapitalize({data : obj.vars.id});
				if (obj.vars.id == 'Reload') {
					insCurrent._eventDetailConnect({flag : id, flagType : insCurrent.insDetail.vars.varsStatus.flagReloadNow});
				}

			} else if (obj.from == 'detail-_mousedownMenu') {
				if (obj.vars.id == 'Reload') {
					return insCurrent.insDetail.vars.varsStatus.flagReloadNow;
				}

			} else if (obj.from == 'detail-_mousedownLine') {
				if (obj.varsTarget) {
					var insEscape = new Code_Lib_Escape();
					var id = insEscape.strLowCapitalize({data : obj.varsTarget});
					insCurrent.insDetail.eventTool({idTarget : obj.vars, flagStr : obj.varsTarget});
					insCurrent._eventDetailConnect({flag : id, flagType : obj.vars});
					return;
				}

			}

		};

		return allot;
	},



	/**
	 *
	*/
	_getNaviAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;
			var array = obj.from.split('-');
			var flagNow = array[0];
			var flagType = array[1];
			var flagBtn = array[2];
			if (flagNow.match(/^folder(.*?)$/)) {
				if (flagType == '_mousedownBtn') {
					var flag = '';
					if (RegExp.$1) flag = 'format' + RegExp.$1;
					else flag = 'format';
					insCurrent._eventDetailConnect({vars : obj.vars, flag : flag});
					insCurrent.insNavi.showBtn();

				} else if (flagType == 'eventBtnBottom') {
					if (flagBtn == 'eventFormBtnAdd') {
						return insCurrent.eventNaviBtnSave();

					} else {
						var flag = '';
						if (RegExp.$1) flag = 'format' + RegExp.$1 + '-save';
						else flag = 'format-save';
						insCurrent._eventNaviConnect({vars : obj.vars, flag : flag});
					}
				}

			}
		};

		return allot;
	},

	/**
	 *
	*/
	eventNaviBtnSave : function()
	{

	},

	/**
	 *
	*/
	_eventListConnect : function(obj)
	{
		if (obj.flag == 'Reload') {
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

		} else if (obj.flag == 'Refresh') {
			obj.flag = 'Search';
			this._eventSearch({
				numLotNow : 0,
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
	eventWindowAppear : function(obj)
	{
		this.varsChild = (Object.toJSON(obj.vars)).evalJSON();
		this._varsSearch = this.insCurrent.getVarsSearch();
		this.vars.portal.varsDetail.varsDetail = this.varsChild.varsDetail;
		this.vars.portal.varsDetail.varsIni = this.varsChild.varsIni;
		this._setDetailStart();
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
			else if (obj.from == '_mousedownMove') {
				insCurrent.insDetail.setValue();
				insCurrent._setDetailContentValue();
				insCurrent.insNavi.eventMove({vars : insCurrent._getDetailFormFormat()});

			}
			else if (obj.from == 'form-preEventLayout') insCurrent._preEventLayoutDetailContent();
			else if (obj.from == 'form-_resetSelectShortCut') insCurrent._eventSelectShortCut();
			else if (obj.from == 'form-eventLayout') insCurrent._eventLayoutDetailContent();
			else if (obj.from == 'form-eventBtnBottom') {
				if (obj.vars.vars.vars.flagBack) {
					insCurrent._backDetailEnd();

				} else if (obj.vars.vars.vars.flagHide) {
					if (!insCurrent.insWindow.vars.flagHideNow) insCurrent.insWindow.updateHide({ flagEffect : 1 });

				} else {
					if (obj.vars.vars.vars.idTarget == 'save') {
						insCurrent._eventDetailConnect({flag : insCurrent.varsChild.flagType});

					} else if (obj.vars.vars.vars.idTarget == 'folder') {
						insCurrent.insDetail.setValue();
						insCurrent._setDetailContentValue();
						var varsFormat = insCurrent._getDetailFormFormat();
						insCurrent.insNavi.addVars({vars : {vars : varsFormat, strTitle : varsFormat.StrTitle}});
						insCurrent.insDetail.showBtnBottom();
					}
				}

			}
		};

		return allot;
	},

	/**
	 *
	*/
	_eventSelectShortCut : function()
	{

	},

	/**
	 *
	*/
	_getDetailFormFormat : function()
	{
		var vars = this.insDetail.getFormValue();
		arr = this.insDetail.insForm.vars.varsDetail;
		for (var i = 0; i < arr.length; i++) {
			if (arr[i].varsFormArea) {
				var data = {};
				data = this._getDetailFormAreaFormat({arr : arr, idTarget : arr[i].id});
				vars[arr[i].id] = data;

			} else if (arr[i].varsFormCheck) {
				var data = {};
				data = this._getDetailFormCheckFormat({arr : arr, idTarget : arr[i].id});
				vars[arr[i].id] = data;

			} else if (arr[i].varsFormList) {
				var data = {};
				data = this._getDetailFormListFormat({arr : arr, idTarget : arr[i].id});
				vars[arr[i].id] = data;

			} else if (arr[i].varsCalenderFormNum) {
				var data = {};
				data = this._getDetailCalenderFormNumFormat({arr : arr, idTarget : arr[i].id});
				vars[arr[i].id] = data;
			}
		}

		return vars;

	},

	/**
	 *
	*/
	_getDetailCalenderFormNumFormat : function(obj)
	{
		var num = 0;
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].varsCalenderFormNum) continue;
			if (obj.idTarget == obj.arr[i].id) {
				return this._varsDetailFormCalenderNum[num].insCalender.vars;
			}
			num++;
		}
	},

	/**
	 *
	*/
	_getDetailFormAreaFormat : function(obj)
	{
		var num = 0;
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].varsFormArea) continue;
			if (obj.idTarget == obj.arr[i].id) {
				return this._varsDetailFormArea[num].insArea.vars.varsDetail;
			}
			num++;
		}
	},


	/**
	 *
	*/
	_getDetailFormCheckFormat : function(obj)
	{
		var num = 0;
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].varsFormCheck) continue;
			if (obj.idTarget == obj.arr[i].id) {
				return this._varsDetailFormCheck[num].insFormCheck.vars.varsDetail;
			}
			num++;
		}
	},

	/**
	 *
	*/
	_getDetailFormListFormat : function(obj)
	{
		var num = 0;
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].varsFormList) continue;
			if (obj.idTarget == obj.arr[i].id) {
				var data = {
					value      : obj.arr[i].value,
					varsDetail : this._varsDetailFormList[num].insList.vars.varsDetail
				};

				return data;
			}
			num++;
		}
	},

	/**
	 *
	*/
	_varsDetailEnd : null,
	_setDetailEnd : function()
	{
		this._varsDetailEnd = (Object.toJSON(this.insDetail.varsEventList)).evalJSON();
		this._getDetailFormContentVars({arr : this.insDetail.insForm.vars.varsDetail});
		this._varsDetailEnd.varsDetail = (Object.toJSON(this.insDetail.insForm.vars.varsDetail)).evalJSON();
		var objData = {
			strTitle : null,
			strClass : null,
			vars     : {
				varsDetail : this.vars.portal.varsDetail.varsEnd.varsDetail,
				varsBtn    : this.vars.portal.varsDetail.varsEnd.varsBtn,
				varsEdit   : {},
				vars       : {}
			}
		};
		this.insDetail.eventList(objData);
	},

	/**
	 *
	*/
	_getDetailFormContentVars : function(obj)
	{

	},

	/**
	 *
	*/
	_backDetailEnd : function()
	{
		this.insDetail.eventList({
			flagMoveUse : 1,
			strTitle    : null,
			strClass    : null,
			vars        : this._varsDetailEnd
		});
		this._setDetailContent();
		this._varsDetailEnd = null;
	},


	/**
	 *
	*/
	_setDetailContentValue : function()
	{

	},


	/**
	 *
	*/
	_eventDetailConnectSuccess : function(obj)
	{
		if (obj.json.flag == 1) {
			if (this._varsDetailConnect.flag == 'add') {
				this.insCurrent.eventDetailConnectSuccessListUpdate(obj);
				this._setDetailEnd();

			} else if (this._varsDetailConnect.flag.match(/^edit/)) {
				this.insCurrent.eventDetailConnectSuccessListDetailUpdate(obj);
				this._setDetailEnd();
			}

		} else if (obj.json.flag == 40) {
			this.insCurrent.eventDetailConnectSuccessListUpdateDetailReset(obj);
			alert(this.insRoot.vars.varsSystem.str.oldData);
			if (!this.insWindow.vars.flagHideNow) this.insWindow.updateHide({ flagEffect : 1 });

		} else if (obj.json.flag == 42) {
			alert(this.insRoot.vars.varsSystem.str.errorMail);

		} else if (obj.json.flag == 8) {
			alert(this.insRoot.vars.varsSystem.str.oldData);

		} else {
			this.insDetail.showFormAttestError({flagType : obj.json.flag});
			if (obj.json) {
				if (obj.json.flag) {
					if (this.insRoot.vars.varsSystem.str[obj.json.flag]) {
						alert(this.insRoot.vars.varsSystem.str[obj.json.flag]);
					}
				}
			}
		}
	}
});

<?php }
}
?>