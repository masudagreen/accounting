<?php /* Smarty version 3.1.24, created on 2019-10-06 06:26:34
         compiled from "/app/rucaro/back/tpl/templates/else/core/base/js/lib/choice.js" */ ?>
<?php
/*%%SmartyHeaderCode:11862384405d99891aac5f91_03603909%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '5ea5e3282b5f1074c3a2a1755eba8a45cca2fe27' => 
    array (
      0 => '/app/rucaro/back/tpl/templates/else/core/base/js/lib/choice.js',
      1 => 1570328740,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '11862384405d99891aac5f91_03603909',
  'variables' => 
  array (
    'varsLoad' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_5d99891ac17417_67873182',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_5d99891ac17417_67873182')) {
function content_5d99891ac17417_67873182 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '11862384405d99891aac5f91_03603909';
?>

/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_Choice = Class.create(Code_Lib_ExtLib,
{


	varsLoad : <?php echo $_smarty_tpl->tpl_vars['varsLoad']->value;?>
,


	/**
	 *
	*/
	iniLoad : function(obj)
	{
		this._iniVars(obj);
	},

	/**
	 *
	*/
	_iniVars : function(obj)
	{
		this._extVars(obj);
	},

	/**
	 * {
		flagId       : '',
		idTarget     : '',
		idModule     : '',
		flagCheckUse : 0,
		strFunc      : '',
		numTop       : 0,
		numLeft      : 0,
		insCurrent   : this
	 * }
	*/
	_varsBoot : null,
	_varsBootTitle : null,
	setBoot : function(obj)
	{
		this._varsBoot = null;
		this._varsBoot = obj;
		var vars = this._checkBoot({
			idTarget  : obj.idTarget,
			idModule  : obj.idModule,
			arr       : this.vars.varsDetail,
			arrStatus : this.insRoot.vars.varsSystem.status.arrModule
		});
		if (!vars) return;

		this._varsBoot.vars = vars;
		this._varsBootTitle = this._varsBoot.vars.varsRequest.idModule
						+ this._varsBoot.vars.varsRequest.strExt
						+ this._varsBoot.vars.varsRequest.strChild;
		this._iniChild();
	},

	/**
	 *
	*/
	_checkBoot : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].idModule == 'Base') {
				if (obj.idTarget == obj.arr[i].id) return obj.arr[i];

			} else {
				var id = obj.arr[i].idModule.toLowerCase();
				if (!obj.arrStatus[id].flagUse) continue;
				if (obj.idTarget == obj.arr[i].id) return obj.arr[i];
			}
		}
	},

	/**
	 *
	*/
	_varsChild : {
		eleLoading : {}, varsWindow : {}
	},
	_iniChild : function()
	{
		var strInsWindow = 'ins' + this._varsBootTitle;

		if (this[strInsWindow]) {

			$(this[strInsWindow].idWindow).setStyle({
				left : this._varsBoot.numLeft + 'px',
				top  : this._varsBoot.numTop + 'px'
			});
			this[strInsWindow + 'Class'].iniReload({
				flagId       : this._varsBoot.flagId,
				flagCheckUse : this._varsBoot.flagCheckUse,
				strFunc      : this._varsBoot.strFunc,
				insReturn    : this._varsBoot.insCurrent,
				varsValue    : (this._varsBoot.varsValue)? this._varsBoot.varsValue : null,
			});
			this[strInsWindow].showLockWindow();

		} else {
			this._varChild();
			this._setChild();
		}
	},

	_varChild : function()
	{
		var vars = (Object.toJSON(this.varsLoad.templateWindow)).evalJSON();
		var str = this._varsBootTitle;
		vars.id =  str;
		vars.strTitle = vars.strTitle.replace(/<?php echo '<%'; ?>
replace<?php echo '%>'; ?>
/, this._varsBoot.vars.strTitle);
		vars.strClass = (this._varsBoot.vars.strClass)? this._varsBoot.vars.strClass : vars.strClass;
		this._varsChild.varsWindow[str] = vars;
	},


	/**
	 *
	*/
	_setChild : function()
	{
		var str = this._varsBootTitle;
		var strInsWindow = 'ins' + str;
		this[strInsWindow] = new Code_Lib_Window();
		this[strInsWindow].iniLoad({
			eleInsert  : $(this.insRoot.vars.varsSystem.id.root),
			insRoot    : this.insRoot,
			insCurrent : this,
			idSelf     : this.idSelf + str,
			allot      : this._getChildAllot(),
			vars       : this._varsChild.varsWindow[str]
		});
	},



	/**
	 *
	*/
	_getChildAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;
			if (obj.from == '_iniVars') insCurrent._updateChild();
			else if (obj.from == '_mousedownBoot') insCurrent.insCurrent._sendChild();
			else if (obj.from.match(/^_mouseupResize$/)) {
				var strInsWindow = 'ins' + insCurrent.insCurrent._varsBootTitle;
				if (insCurrent.insCurrent[strInsWindow + 'Class']) {
					insCurrent.insCurrent[strInsWindow + 'Class'].eventWindow();
				}
			}
		};

		return allot;
	},


	/**
	 *
	*/
	_updateChild : function()
	{
		var str = this._varsBootTitle;
		var strInsWindow = 'ins' + str;
		this[strInsWindow].vars.numZIndex = this.insRoot.setZIndex();
		this[strInsWindow].vars.numTop = this._varsBoot.numTop;
		this[strInsWindow].vars.numLeft = this._varsBoot.numLeft;
		this[strInsWindow].vars.flagHideNow = 0;
	},

	/**
	 *
	*/
	_sendChild : function()
	{
		var arrayKey = [], arrayValue = [];
		arrayKey = ['class', 'module', 'ext', 'child', 'func', 'db'];
		arrayValue = [
			this._varsBoot.vars.varsRequest.strClass,
			this._varsBoot.vars.varsRequest.idModule,
			this._varsBoot.vars.varsRequest.strExt,
			this._varsBoot.vars.varsRequest.strChild,
			this._varsBoot.vars.varsRequest.strFunc,
			'slave',
		];
		if (this._varsBoot.varsValue) {
			var jsonValue = Object.toJSON(this._varsBoot.varsValue);
			arrayKey.push('jsonValue');
			arrayValue.push(jsonValue);
		}

		this.insRoot.insRequest.set({
			flagLock        : 0,
			numZIndex       : this.insRoot.getZIndex(),
			insCurrent      : this,
			flagEscape      : 1,
			path            : this.insRoot.vars.varsSystem.path.post,
			querysKey       : arrayKey,
			querysValue     : arrayValue,
			functionSuccess : '_sendChildSuccess',
			functionFail    : '_sendChildFail'
		});

		var str = this._varsBootTitle;
		var strInsWindow = 'ins' + str;
		var ele = $(document.createElement('span'));
		$(this[strInsWindow].idWindow).down('.codeLibWindowBodyTopMiddleWrap', 0).insert(ele);
		ele.addClassName('codeLibRequestImgLoading');
		ele.addClassName('codeLibRequestImgLoadingPos');
		this._varsChild.eleLoading[str] = ele;
	},

	/**
	 *
	*/
	_sendChildSuccess : function(obj)
	{
		var strExt = this._varsBoot.vars.varsRequest.strExt;
		var strChild = this._varsBoot.vars.varsRequest.strChild;
		var strClass = this._varsBoot.vars.varsRequest.strClass;
		var idModule = this._varsBoot.vars.varsRequest.idModule;
		var id = this._varsBoot.vars.id;

		if (obj.response.responseText.isJSON()) {
			var json = obj.response.responseText.evalJSON();
			if (json.flag == 0) {
				alert(this.insRoot.vars.varsSystem.str.errorSession);
				return;
			}
		}

		var eleScript = $(document.createElement('script'));
		eleScript.type = 'text/javascript';
		eleScript.text = obj.response.responseText;
		var eleHead = document.getElementsByTagName('head').item(0);
		eleHead.appendChild(eleScript);

		var newClass = eval('Code_' + strClass + '_' + idModule + '_' + strExt + strChild);
		var str = this._varsBootTitle;
		var strInsWindow = 'ins' + str;
		this[strInsWindow + 'Class'] = new newClass();
		this[strInsWindow + 'Class'].iniLoad({
			eleInsert    : $(this[strInsWindow].idWindow).down('.codeLibWindowBodyTopMiddleWrap', 0),
			insRoot      : this.insRoot,
			insCurrent   : this,
			idSelf       : this.idSelf + strExt + strChild + id,
			strExt       : strExt,
			strChild     : strChild,
			strClass     : strClass,
			idModule     : idModule,
			flagCheckUse : this._varsBoot.flagCheckUse,
			strFunc      : this._varsBoot.strFunc,
			insReturn    : this._varsBoot.insCurrent,
			flagId       : this._varsBoot.flagId,
			varsValue    : (this._varsBoot.varsValue)? this._varsBoot.varsValue : null,
			insWindow    : this[strInsWindow]
		});
		this._varsChild.eleLoading[str].remove();
		this._varsChild.eleLoading[str] = null;
	},

	/**
	 *
	*/
	_sendChildFail : function(obj)
	{
		var str = this._varsBootTitle;
		this._varsChild.eleLoading[str].remove();
		this._varsChild.eleLoading[str] = null;
		alert(this.insRoot.vars.varsSystem.str.errorConnect);
	}

});
<?php }
}
?>