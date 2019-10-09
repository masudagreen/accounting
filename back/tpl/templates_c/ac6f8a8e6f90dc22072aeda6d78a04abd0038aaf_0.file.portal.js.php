<?php /* Smarty version 3.1.24, created on 2016-08-18 12:50:43
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/portal.js" */ ?>
<?php
/*%%SmartyHeaderCode:160949635657b5af23547278_40237522%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ac6f8a8e6f90dc22072aeda6d78a04abd0038aaf' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/portal.js',
      1 => 1471523677,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '160949635657b5af23547278_40237522',
  'variables' => 
  array (
    'varsLoad' => 0,
    'numNews' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_57b5af236b4a17_32301951',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_57b5af236b4a17_32301951')) {
function content_57b5af236b4a17_32301951 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '160949635657b5af23547278_40237522';
?>

/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Core_Base_Portal = Class.create(Code_Lib_ExtPortal,
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
			strTitleChild  : 'Account',
			strExt         : 'Account',
			strClass       : 'Core',
			strChild       : '',
			idModule       : 'Base'
		});



		this._iniChild({
			strTitleParent : this.insWindow.vars.strTitle,
			strTitleChild  : 'Term',
			strExt         : 'Term',
			strClass       : 'Core',
			strChild       : '',
			idModule       : 'Base'
		});
*/
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
		this.insRoot.vars.varsSystem.token = this.vars.token;
		this.strClass = this.vars.strClass;
		this.idModule = this.vars.idModule;
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
	 *
	*/
	_sendNaviConnect : function()
	{
		var str = this.strClass
			+ '-' + this.idModule
			+ '-' + this.strExt
			+ '-' + this.strChild
			+ '-' + 'NaviReload';
		var objStamp = {
			id    : str,
			stamp : (this._varsStamp[str])? this._varsStamp[str] : 0
		};
		var jsonStamp = (Object.toJSON(objStamp));
		var arrayKey = [], arrayValue = [];

		arrayKey = ['class', 'module', 'ext', 'child', 'func', 'db', 'jsonStamp'];
		arrayValue = [this.strClass, this.idModule, this.strExt, this.strChild, 'NaviReload', 'slave', jsonStamp];

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
	_eventNaviConnectSuccess : function(obj)
	{
		this.insLayout.updateTool({flagLock : 0, from : 'navi', idTarget : 'Reload'});
		if (obj.json.flag == 1) this.insNavi.updateTreeVars({vars : obj.json.data});
		else if (obj.json.flag == 0) alert(this.insRoot.vars.varsSystem.str.errorSession);
		else if (obj.json.flag == 4) alert(this.insRoot.vars.varsSystem.str.maintenance);
		else if (obj.json.flag == 8) alert(this.insRoot.vars.varsSystem.str.oldData);
		else {
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
	_varsDetailEnd : null,
	_setDetailEnd : function()
	{
		this._varsDetailEnd = (Object.toJSON(this.insDetail.varsEventNavi)).evalJSON();
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
		this.insDetail.eventNavi(objData);
	},

	/**
	 *
	*/
	_setDetailReset : function()
	{
		var objData = {
			strTitle : null,
			strClass : null,
			vars     : {
				varsDetail : this.vars.portal.varsDetail.varsEnd.varsDetail,
				varsBtn    : [],
				varsEdit   : {},
				vars       : {}
			}
		};
		this.insDetail.eventNavi(objData);
	},

	/**
	 *
	*/
	_backDetailEnd : function()
	{
		this._setNaviDetail({vars : this._varsDetailEnd.vars});
		this._varsDetailEnd = null;
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
				strExt         : strExt,
				strChild       : '',
				strClass       : this.strClass,
				idModule       : this.idModule
			});
		}
		else this._setNaviDetail({vars : obj.vars});
	},

	/**
	 *
	*/
	_setNaviDetail : function(obj)
	{
		var objDetail;
		if (obj.vars.vars.idTarget == 'strCodeName') {
			objDetail = this._setNaviDetailStrCodeName({arr : (Object.toJSON(obj.vars.vars.varsDetail)).evalJSON()});

		} else if (obj.vars.vars.idTarget == 'strPassword') {
			objDetail = this._setNaviDetailStrPassword({arr : (Object.toJSON(obj.vars.vars.varsDetail)).evalJSON()});

		} else {
			objDetail = obj.vars.vars.varsDetail;

		}
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
		this._setDetailContent({idTarget : obj.vars.vars.idTarget});
	},


	/**
	 *
	*/
	_setNaviDetailStrPassword : function(obj)
	{
		var insDisplay = new Code_Lib_TimeDisplay();
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == 'DummyPassword') {
				var objTime = this.insRoot.insTimeZone.adjustDate({stamp : obj.arr[i].stamp * 1000});
				var strTime = insDisplay.get({flagType : 1, vars : objTime});
				obj.arr[i].strComment = obj.arr[i].strComment.replace(/<?php echo '<%'; ?>
replace<?php echo '%>'; ?>
/, strTime);
			}
		}

		return obj.arr;
	},

	/**
	 *
	*/
	_setNaviDetailStrCodeName : function(obj)
	{
		var insDisplay = new Code_Lib_TimeDisplay();
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == 'DummyAccount') {
				var objTime = this.insRoot.insTimeZone.adjustDate({stamp : obj.arr[i].stamp * 1000});
				var strTime = insDisplay.get({flagType : 1, vars : objTime});
				obj.arr[i].strComment = obj.arr[i].strComment.replace(/<?php echo '<%'; ?>
replace<?php echo '%>'; ?>
/, strTime);

			}
		}

		return obj.arr;
	},

	/**
	 *
	*/
	_setDetailContent : function(obj)
	{
		if (obj.idTarget == 'jsonIpSignReject'
			|| obj.idTarget == 'jsonMailSignReject'
			|| obj.idTarget == 'jsonIpAccessAccept'
			|| obj.idTarget == 'jsonIpAccessReject'
		) {
			this._iniDetailFormList();

		} else if (obj.idTarget == 'arrCommaIdAccountMaintenance') {
			this._iniDetailFormArea();
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
	_iniDetailFormArea : function()
	{
		this._extDetailFormArea();
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
	_eventLayoutDetailContent : function()
	{
		if (!this.insDetail.varsEventNavi) return;
		if (!this.insDetail.varsEventNavi.vars.vars) return;
		var idTarget = this.insDetail.varsEventNavi.vars.vars.idTarget;
		if (idTarget == 'jsonIpSignReject'
			|| idTarget == 'jsonMailSignReject'
			|| idTarget == 'jsonIpAccessAccept'
			|| idTarget == 'jsonIpAccessReject'
		) {
			this._iniDetailFormList();

		} else if (idTarget == 'arrCommaIdAccountMaintenance') {
			this._iniDetailFormArea();
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
		if (idTarget == 'jsonIpSignReject'
			|| idTarget == 'jsonMailSignReject'
			|| idTarget == 'jsonIpAccessAccept'
			|| idTarget == 'jsonIpAccessReject'
		) {
			this._getDetailFormListVars({arr : this.insDetail.insForm.vars.varsDetail});

		} else if (idTarget == 'arrCommaIdAccountMaintenance') {
			this._getDetailFormAreaVars({arr : this.insDetail.insForm.vars.varsDetail});

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
		if (idTarget == 'jsonIpSignReject'
			|| idTarget == 'jsonMailSignReject'
			|| idTarget == 'jsonIpAccessAccept'
			|| idTarget == 'jsonIpAccessReject'
		) {
			this._eventRemoveDetailFormList({arr : this.insDetail.insForm.vars.varsDetail});

		} else if (idTarget == 'arrCommaIdAccountMaintenance') {
			this._eventRemoveDetailFormArea({arr : this.insDetail.insForm.vars.varsDetail});

		}
	},


	/**
	 *
	*/
	_numVersionTry : 0,
	_eventDetailConnect : function(obj)
	{
		this._numVersionTry = 0;
		if (obj.flag == 'reload') {
			this._eventValue({
				vars     : '',
				idTarget : obj.idTarget
			});

		} else if (obj.flag == 'edit') {
			if (obj.idTarget == 'arrCommaIdAccountMaintenance') {
				this._setDetailFormAreaValue({arr : this.insDetail.insForm.vars.varsDetail});

			} else if (obj.idTarget == 'jsonIpSignReject'
				|| obj.idTarget == 'jsonMailSignReject'
				|| obj.idTarget == 'jsonIpAccessAccept'
				|| obj.idTarget == 'jsonIpAccessReject'
			) {
				this._setDetailFormListValue({arr : this.insDetail.insForm.vars.varsDetail});

			} else if (obj.idTarget == 'local') {
				this._iniLocal();
				return;
			}

			if (this.insDetail.checkForm({flagType : 'common'})) return;
			var vars = this.insDetail.getFormValue();
			if (obj.idTarget == 'strPassword') {
				if (vars.StrPassword != vars.StrPasswordConfirm) {
					this.insDetail.showFormAttestError({flagType : 'common'});
					return;
				}

			} else if (obj.idTarget == 'strSiteName') {
				var strUrl = location.href;
				var arrTemp = strUrl.split('/');
				var strDomainUrl = arrTemp[2];
				arrTemp = vars.StrSiteMailPc.split('@');
				var strDomainMail = arrTemp[1];
				if (strDomainUrl != strDomainMail) {
					var strConfirm = this.vars.varsItem.strDomain;
					strConfirm = strConfirm.replace(/<?php echo '<%'; ?>
strDomainUrl<?php echo '%>'; ?>
/, strDomainUrl);
					strConfirm = strConfirm.replace(/<?php echo '<%'; ?>
strDomainMail<?php echo '%>'; ?>
/, strDomainMail);
					if (!window.confirm(strConfirm)) {
						this.insDetail.showBtnBottom();
						return;
					}
				}

			} else if (obj.idTarget == 'version') {
				vars.FlagVersionUpdate = 0;
				if (obj.id == 'FlagTrue') {
					vars.FlagVersionUpdate = 1;

				} else if (obj.id == 'FlagFalse') {
					vars.FlagVersionUpdate = 2;
				}
			}

			this._eventValue({
				vars     : vars,
				idTarget : obj.idTarget
			});

		}
		this._varsDetailConnect = obj;
		this._sendDetailConnect();
	},

	/**
	 *
	*/
	_varsBlock : {},
	_getBlock : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].vars.idTarget == obj.idTarget) {
				this._varsBlock = (Object.toJSON(obj.arr[i])).evalJSON();
			}
			else this._getBlock({arr : obj.arr[i].child, idTarget : obj.idTarget});
		}
	},


	/**
	 *
	*/
	_eventDetailConnectSuccess : function(obj)
	{
		if (obj.json.flag == 1) {
			if (obj.json.data.idTarget == 'numAutoMustLogout'
				|| obj.json.data.idTarget == 'numPasswordLimit'
			) {
				this.insNavi.updateTreeVars({vars : obj.json.data.vars});
				if (this._varsDetailConnect.flag == 'reload') {
					if (this.insDetail.varsEventNavi.vars.vars.idTarget == obj.json.data.vars.idTarget) {
						this._setNaviDetail({vars : obj.json.data});
					}

				} else if (this._varsDetailConnect.flag == 'edit') {
					this._getBlock({
						arr      : obj.json.data.vars,
						idTarget : obj.json.data.idTarget
					});
					this._setNaviDetail({vars : this._varsBlock});
					this._setDetailEnd();
				}

			} else {
				if (this._varsDetailConnect.flag == 'reload') {
					if (this.insDetail.varsEventNavi.vars.vars.idTarget == obj.json.data.vars.idTarget) {
						this._setNaviDetail({vars : obj.json.data});
					}

				} else if (this._varsDetailConnect.flag == 'edit') {
					if (obj.json.data.vars.idTarget == 'version') {
						if (obj.json.data.vars.flagVersion == 'dll') {
							this._setNaviDetail({vars : obj.json.data});
							return;

						} else if (obj.json.data.vars.flagVersion == 'flagVersionUpdate') {
							if (this.insDetail.varsEventNavi.vars.vars.idTarget == obj.json.data.vars.idTarget) {
								this._setNaviDetail({vars : obj.json.data});
								this._setDetailEnd();
								if (obj.json.stamp) {
									var data = (Object.toJSON(obj.json.data)).evalJSON();
									if (obj.json.stamp.id) this._varsStampCheck[obj.json.stamp.id] = data;
								}
								this.insNavi.updateTreeVarsDetail({vars : obj.json.data});
							}
							return;
						}
						this._setDetailEnd();
						this.insDetail.hideBtnBottom();
						this.insRoot.iniPopup({flag : 'reload'});
						setTimeout(function() { location.href = this.insRoot.vars.varsSystem.path.post;}, 3000);
						return;
					}
					if (this.insDetail.varsEventNavi.vars.vars.idTarget == obj.json.data.vars.idTarget) {
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

		} else if (obj.json.flag == 'strPassword') {
			if (this.insDetail.varsEventNavi.vars.vars.idTarget == obj.json.data.idTarget) {
				this.insDetail.showFormAttestError({flagType : 'sameValue'});
			}

		} else if (obj.json.flag == 'strCodeName') {
			if (this.insDetail.varsEventNavi.vars.vars.idTarget == obj.json.data.idTarget) {
				this.insDetail.showFormAttestError({flagType : 'common'});
			}
		} else if (obj.json.flag == 'connect') {
			if (this.insDetail.varsEventNavi.vars.vars.idTarget == obj.json.data.idTarget) {
				this.insDetail.viewForm({
					idTarget    : 'DummyError',
					flagHideNow : 0
				});
				this.insDetail.hideBtnBottom();
			}

		} else if (obj.json.flag == 'dlled') {
			if (this.insDetail.varsEventNavi.vars.vars.idTarget == obj.json.data.idTarget) {
				if (this._numVersionTry) {
					this.insDetail.viewForm({
						idTarget    : 'DummyError',
						flagHideNow : 0
					});

				} else {
					this.insDetail.viewForm({
						idTarget    : 'DummyUpdateNow',
						flagHideNow : 0
					});
				}
				this.insDetail.hideBtnBottom();
			}
			if (!this._numVersionTry) {
				this._numVersionTry = 1;
				this._sendDetailConnect();
			}

		} else if (obj.json.flag == 'strSiteMailPc') {
			if (this.insDetail.varsEventNavi.vars.vars.idTarget == obj.json.data.idTarget) {
				this.insDetail.showFormAttestError({flagType : 'strSiteMailPc'});
			}

		} else if (obj.json.flag == 'strIpSelf') {
			if (this.insDetail.varsEventNavi.vars.vars.idTarget == obj.json.data.idTarget) {
				this.insDetail.showFormAttestError({flagType : obj.json.flag});
			}
		}
	},

	/**
	 *
	*/
	_iniLocal : function()
	{
		this.insRoot.insCake.removeStorageAllCake();
		this.insRoot.iniPopup({flag : 'reload'});
		setTimeout(function() { location.href = this.insRoot.vars.varsSystem.path.post;}, 3000);
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