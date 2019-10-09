<?php /* Smarty version 3.1.24, created on 2019-06-16 09:02:07
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/form.js" */ ?>
<?php
/*%%SmartyHeaderCode:8194477715d06058fead056_50483179%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '401eb86d621590e7f7b077566eb330cd8af60e73' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/form.js',
      1 => 1560675139,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '8194477715d06058fead056_50483179',
  'variables' => 
  array (
    'varsLoad' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_5d06058feb5ff3_95222838',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_5d06058feb5ff3_95222838')) {
function content_5d06058feb5ff3_95222838 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '8194477715d06058fead056_50483179';
?>

/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_Form = Class.create(Code_Lib_ExtLib,
{
	
		varsLoad : <?php echo $_smarty_tpl->tpl_vars['varsLoad']->value;?>
,
	
	/**
	 *
	*/
	initialize : function(obj)
	{
		this._extAllot(obj);
		this._iniVars(obj);
		this._iniListener();
		this._iniListenerSelectShortCut();
		this._iniWrap();
		this._iniTemplate();
		this._iniBtnBottom();
		this.setFieldFocus();
	},

	/**
	 *
	*/
	iniReload : function(obj)
	{
		this.getScroll();
		this.setValue();
		this.stopListener();
		this.stopListenerSelectShortCut();
		this.removeWrap();
		this._iniWrap();
		this._iniTemplate();
		this._iniBtnBottom();
		this.setScroll();
	},

	/**
	 *
	*/
	getVarsScroll : function()
	{
		var data = {
			numTop  : this.eleInsert.scrollTop,
			numLeft : this.eleInsert.scrollLeft
		};

		return data;
	},

	/**
	 *
	*/
	setFieldFocus : function(obj)
	{
		if (obj == undefined) {
			idTarget = '';
		} else {
			if (obj.idTarget == undefined) {
				idTarget = '';
			} else {
				idTarget = obj.idTarget;
			}
		}
		this._setFieldFocus({arr : this.vars.varsDetail, idTarget : idTarget});
	},

	/**
	 *
	*/
	_setFieldFocus : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].flagCommentUse) continue;
			if (obj.arr[i].flagContentUse) continue;
			if (obj.arr[i].flagHideUse && obj.arr[i].flagHideNow) continue;
			if (obj.arr[i].flagDisabled) continue;
			if (obj.idTarget === '') {
				if ($(this.idSelf + obj.arr[i].id)) {
					$(this.idSelf + obj.arr[i].id).focus();
					return;
				}

			} else {
				if (obj.idTarget == obj.arr[i].id) {
					if ($(this.idSelf + obj.arr[i].id)) {
						$(this.idSelf + obj.arr[i].id).focus();
						return;
					}
				}
			}
		}
	},

	/**
	 *
	*/
	setVarsScroll : function(obj)
	{
		this.eleInsert.scrollTop = obj.numTop;
		this.eleInsert.scrollLeft = obj.numLeft;
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
	_iniListener : function()
	{
		this._extListener();
	},

	/**
	 * Wrap
	*/
	_iniWrap : function()
	{
		this._extWrap();
	},

	/**
	 * Btn
	*/
	_iniBtnBottom : function()
	{
		this._extBtnBottom();
	},

	/**
	 * Template
	*/
	_iniTemplate : function(obj)
	{
		this._setTemplate({arr : this.vars.varsDetail});
		this._setTemplateContent({arr : this.vars.varsDetail});
	},

	/**
	 *
	*/
	_checkFormFile : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].flagInputType == 'file') {
				return 1;
			}
		}

		return 0;
	},

	/**
	 *
	 */
	_staticContent : {numMargin : 20, numPadding : 10, numIdle : 10},
	_getContentWidth : function()
	{
		var array = this.eleInsert.style.width.split('px');
		var num = parseFloat(array[0]);

		if(!isFinite(num)){
			num = this.eleInsert.offsetWidth;
		}

		var data = num
			 - this._staticContent.numMargin
			 - this._staticContent.numPadding * 2
			 - this._staticContent.numIdle;

		return data;
	},

	/**
	 *
	*/
	eleForm : null,
	eleIframe : null,
	_setTemplate : function(obj)
	{
		var flagFile = this._checkFormFile({arr : obj.arr});
		var eleForm = $(document.createElement('form'));
		this.eleForm = eleForm;
		this.eleWrap.insert(eleForm);

		for (var i = 0; i < obj.arr.length; i++) {
			/*wrap*/
			var eleWrap = $(document.createElement('div'));
			eleWrap.addClassName('codeLibFormWrap');
			eleWrap.id= this.idSelf + obj.arr[i].id + 'Wrap';
			if (obj.arr[i].flagHideUse && obj.arr[i].flagHideNow) eleWrap.hide();
			else eleWrap.show();
			eleWrap.addClassName('clearfix');
			eleForm.insert(eleWrap);

			/*title*/
			var eleTitle = $(document.createElement('div'));
			eleTitle.addClassName('codeLibFormTitle');
			if(obj.arr[i].flagCommentUse && obj.arr[i].strCommentTitle) {
				eleTitle.addClassName('codeLibFormComment');
				eleTitle.insert(obj.arr[i].strCommentTitle);
			} else {
				if(obj.arr[i].flagMustUse) {
					eleTitle.addClassName('codeLibFormMust');
				}
				else eleTitle.addClassName('codeLibFormNone');
				eleTitle.insert(obj.arr[i].strTitle);
			}
			eleWrap.insert(eleTitle);

			/*explain*/
			var eleExplain = $(document.createElement('div'));
			eleWrap.insert(eleExplain);
			if(obj.arr[i].flagCommentUse) {
				if(obj.arr[i].strCommentTitle) {
					eleExplain.addClassName('codeLibFormExplain');
					eleExplain.addClassName('codeLibBaseFontSizeSeventy');
				}
				else  eleExplain.addClassName('codeLibFormCommentExplain');
				eleExplain.insert(obj.arr[i].strComment);
				continue;

			} else {
				eleExplain.addClassName('codeLibBaseFontSizeSeventy');
				eleExplain.addClassName('codeLibFormExplain');
				eleExplain.insert(obj.arr[i].strExplain);
			}

			/*error*/
			var eleError = $(document.createElement('div'));
			eleError.addClassName('codeLibFormError');
			eleError.addClassName('codeLibBaseFontSizeSeventy');
			eleError.addClassName('codeLibBaseFontRed');
			eleError.hide();
			eleWrap.insert(eleError);

			/*form*/
			var eleTag;
			if(obj.arr[i].flagTag == 'input' && obj.arr[i].flagTag == 'checkbox') {
				eleTag = $(document.createElement('ul'));

			} else if(obj.arr[i].flagTag == 'selectShortCut') {
				eleTag = $(document.createElement('input'));

			} else {
				eleTag = $(document.createElement(obj.arr[i].flagTag));
			}
			eleTag.addClassName('codeLibFormTag');

			eleTag.id = this.idSelf + obj.arr[i].id;
			if (flagFile) {
				if (obj.arr[i].flagMultiple) {
					eleTag.name = obj.arr[i].id + '[]';
					eleTag.multiple = true;
				} else {
					eleTag.name = obj.arr[i].id;
				}
			}


			/*flagInputType*/
			if(obj.arr[i].flagTag == 'input') {
				eleTag.type = obj.arr[i].flagInputType;

			} else if(obj.arr[i].flagTag == 'selectShortCut') {
				eleTag.type = 'text';
			}

			/*style*/
			eleTag.style.width = obj.arr[i].numWidth + obj.arr[i].unitWidth;
			if(obj.arr[i].flagTag == 'textarea') {
				eleTag.style.height = obj.arr[i].numHeight + obj.arr[i].unitHeight;
			}

			/*numMaxlength*/
			if(obj.arr[i].numMaxlength) {
				if (obj.arr[i].flagInputType != 'file') eleTag.maxLength = obj.arr[i].numMaxlength;
			}

			if (obj.arr[i].arrayHidden) {
				if (obj.arr[i].arrayHidden.length) {
					var arr = obj.arr[i].arrayHidden;
					for (var j = 0; j < arr.length; j++) {
						var eleHidden = $(document.createElement('input'));
						eleHidden.type  = 'hidden';
						eleHidden.id    = arr[j].id;
						eleHidden.name  = arr[j].id;
						eleHidden.value = arr[j].value;
						eleWrap.insert(eleHidden);
					}
				}
			}

			/*disabled*/
			if(obj.arr[i].flagDisabled) eleTag.disabled = true;

			/*value*/
			if (obj.arr[i].flagInputType != 'file') eleTag.value = obj.arr[i].value;

			if(obj.arr[i].flagTag == 'select') {
				if(obj.arr[i].flagMultiple) eleTag.multiple = true;
				if(obj.arr[i].numSize) eleTag.size = obj.arr[i].numSize;
				this._setTemplateSelect({
					arr       : obj.arr[i].arrayOption,
					now       : obj.arr[i].value,
					eleInsert : eleTag,
					vars      : obj.arr[i]
				});

			} else if(obj.arr[i].flagTag == 'selectShortCut') {
				this._setTemplateSelectShortCut({
					arr       : obj.arr[i].arrayOption,
					now       : obj.arr[i].value,
					eleTag    : eleTag,
					eleWrap   : eleWrap,
					vars      : obj.arr[i]
				});

			} else if(obj.arr[i].flagTag == 'input' && obj.arr[i].flagTag == 'checkbox') {
				this._setTemplateCheckbox({
					arr       : obj.arr[i].arrayOption,
					now       : obj.arr[i].value,
					eleInsert : eleTag
				});

			}

			/*content*/
			if(obj.arr[i].flagContentUse) {
				var ele = $(document.createElement('div'));
				ele.addClassName('codeLibFormContent');
				ele.style.width = this._getContentWidth() + 'px';
				eleWrap.insert(ele);

			}


			if(obj.arr[i].flagTag == 'textarea' && obj.arr[i].numMaxlength) {
				var eleKeyWrap = $(document.createElement('div'));
				eleKeyWrap.id = this.idSelf + obj.arr[i].id + 'KeyWrap';
				eleKeyWrap.addClassName('codeLibFormTag');
				eleWrap.insert(eleKeyWrap);
				this.insListener.set({
					bindAsEvent : 1, insCurrent : this, event : 'keyup',
					strFunc : '_keyupTextArea', ele : eleTag, vars : {vars : obj.arr[i], ele : eleTag, eleLength : eleKeyWrap}
				});
				this._resetTextArea({vars : obj.arr[i], ele : eleTag, eleLength : eleKeyWrap});
			}

			eleWrap.insert(eleTag);


		}

		if (flagFile) {
			var cache = (new Date()).getTime();
			eleForm.action = this.insRoot.vars.varsSystem.path.post;
			eleForm.target = this.idSelf + 'Upload' + cache;
			eleForm.method = 'POST';
			eleForm.enctype = 'multipart/form-data';
			var eleIframe = $(document.createElement('iframe'));
			eleIframe.name = this.idSelf + 'Upload' + cache;
			eleIframe.setStyle({
				width  : '0px',
				height : '0px',
				border : '0px'
			});
			eleIframe.src = '';
			this.eleIframe = eleIframe;
			$(this.insRoot.vars.varsSystem.id.temp).insert(eleIframe);
		}
	},

	_keyupTextArea : function (evt, obj)
	{
		this._resetTextArea(obj);
	},

	_resetTextArea : function (obj)
	{
		var numLength = obj.ele.value.length;
		var strLength = '( ' + numLength + this.varsLoad.strStr + ' )';
		var numMax = obj.vars.numMaxlength;
		var strMax = '( ' + numMax + this.varsLoad.strStr + ' )';
		var str = obj.ele.value.substr(0, numMax);
		obj.eleLength.innerHTML = '';
		obj.eleLength.insert(strLength);
		if (numLength > numMax) {
			alert(this.varsLoad.strMax);
			obj.ele.value = str;
			obj.eleLength.innerHTML = '';
			obj.eleLength.insert(strMax);
		}
	},

	/**
	 *
	*/
	resetIframe : function()
	{
		this.eleIframe.innerHtml = '';
	},

	/**
	 *
	*/
	setTemplateSelect : function(obj)
	{
		this._setTemplateSelect(obj);
	},

	/**
	 *
	*/
	_setTemplateSelect : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			var ele = $(document.createElement('option'));
			ele.value = obj.arr[i].value;
			if (obj.vars.flagMultiple) {
				if (parseFloat(obj.vars.value[obj.arr[i].value])) ele.selected = true;
			} else {
				if(obj.now == obj.arr[i].value) ele.selected = true;
			}

			if (obj.arr[i].flagDisabled)  ele.disabled = true;
			ele.insert(obj.arr[i].strTitle);
			obj.eleInsert.insert(ele);
		}
		if (obj.flagDisabled == 1)  obj.eleInsert.disabled = true;
		else if (obj.flagDisabled == 0)  obj.eleInsert.disabled = false;
	},

	/**
	 * Listener
	*/
	insListenerSelectShortCut : null,
	_iniListenerSelectShortCut : function()
	{
		this.insListenerSelectShortCut = new Code_Lib_Listener();
		this._varsListenerSelectShortCut = [];
	},

	/**
	 *
	*/
	_varsListenerSelectShortCut : null,
	_setListenerSelectShortCut : function(obj)
	{
		var data = {ins : obj.ins};
		this._varsListenerSelectShortCut.push(data);
	},

	/**
	 *
	*/
	stopListenerSelectShortCut : function()
	{
		this.insListenerSelectShortCut.stop();
		this._stopListenerChild({arr : this._varsListenerSelectShortCut});
		this._resetListenerSelectShortCut();
	},

	/**
	 *
	*/
	_resetListenerSelectShortCut : function()
	{
		this._varsListenerSelectShortCut = [];
	},

	/**
	 *
	*/
	_setTemplateSelectShortCut : function(obj)
	{
		var numWidth = this._getContentWidth();
		if (obj.vars.unitWidth == '%') {
			numWidth = parseFloat(numWidth * obj.vars.numWidth/100);
		} else {
			if (parseFlaot(obj.vars.numWidth)) {
				numWidth = parseFloat(numWidth * obj.vars.numWidth/100);
			}
		}

		var eleSelectWrap = $(document.createElement('div'));
		eleSelectWrap.addClassName('codeLibFormSelectBtnWrap');
		eleSelectWrap.id = this.idSelf + obj.vars.id + 'SelectBtnWrap';
		eleSelectWrap.style.width = numWidth + 'px';
		eleSelectWrap.addClassName('clearfix');
		eleSelectWrap.unselectable = 'on';
		eleSelectWrap.addClassName('unselect');

		var strTitle = '';
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.now == obj.arr[i].value) {
				strTitle = obj.arr[i].strTitle;
			}
		}

		/*title*/
		var eleTitle = $(document.createElement('span'));
		eleTitle.addClassName('codeLibFormSelectBtnTitleWrap');
		eleTitle.insert(strTitle);
		eleTitle.title = strTitle;
		eleTitle.style.width = (numWidth - 25) + 'px';
		eleSelectWrap.insert(eleTitle);

		/*img*/
		var eleImg = $(document.createElement('span'));
		eleImg.addClassName('codeLibFormSelectBtn');
		eleSelectWrap.insert(eleImg);
		obj.eleWrap.insert(eleSelectWrap);
		obj.eleTag.hide();

		this.insListenerSelectShortCut.set({
			bindAsEvent : 1, insCurrent : this, event : 'mousedown',
			strFunc : '_mousedownSelectShortCut', ele : eleSelectWrap, vars : { vars : obj.vars ,eleWrap : eleSelectWrap ,eleTitle : eleTitle}
		});
	},



	/**
	 *
	*/
	_mousedownSelectShortCut : function(evt, obj)
	{
		evt.stop();
		var varsStyle = this.getVarsScroll();
		var vars = {
			flagTag       : 'selectShortCut',
			flagInputType : '',
			numMaxlength  : 0,
			numWidth      : 0,
			unitWidth     : 'px',
			numHeight     : 0,
			unitHeight    : 'px',
			arrayOption   : obj.vars.arrayOption,
			value         : obj.vars.value,
			vars          : obj.vars
		};

		var varsFormTemp = (Object.toJSON(obj.vars.varsFormTemp)).evalJSON();
		varsFormTemp.varsStatus.numTop = obj.eleWrap.offsetTop - varsStyle.numTop;
		varsFormTemp.varsStatus.numLeft = obj.eleWrap.offsetLeft - varsStyle.numLeft;

		varsFormTemp.varsDetail = vars;
		varsFormTemp.varsDetail.numWidth = obj.eleWrap.offsetWidth;
		varsFormTemp.varsDetail.numHeight = obj.eleWrap.offsetHeight;

		this.insFormTemp = new Code_Lib_FormTemp({
			insRoot    : this.insRoot,
			eleInsert  : $(this.idSelf).up('.codeLibWindow', 0),
			insCurrent : this,
			idSelf     : this.idSelf + 'Edit',
			allot      : this._getSelectShortCutAllot(),
			vars       : varsFormTemp
		});
	},

	/**
	 *
	 */
	_getSelectShortCutAllot : function()
	{
		var allot = function(obj) {
			var insCurrent = obj.insCurrent;
			if (obj.from == 'removeWrap') {
				insCurrent._setValueSelectShortCut({
					arr   : insCurrent.vars.varsDetail,
					value : obj.vars.value,
					vars  : obj.vars.vars
				});
			}
		};

		return allot;
	},

	/**
	 *
	*/
	_setValueSelectShortCut : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == obj.vars.id) {
				obj.arr[i].value = obj.value;
				this._resetSelectShortCut({vars : obj.arr[i]});
				break;
			}
		}

	},

	/**
	 *
	*/
	_resetSelectShortCut : function(obj)
	{
		this.stopListenerSelectShortCut();
		var eleTag = $(this.idSelf + obj.vars.id);
		eleTag.value = obj.vars.value;
		var eleWrap = $(this.idSelf + obj.vars.id + 'Wrap');
		$(this.idSelf + obj.vars.id + 'SelectBtnWrap').remove();
		this._setTemplateSelectShortCut({
			arr       : obj.vars.arrayOption,
			now       : obj.vars.value,
			eleTag    : eleTag,
			eleWrap   : eleWrap,
			vars      : obj.vars
		});
		this.allot({
			insCurrent : this.insCurrent,
			from       : '_resetSelectShortCut'
		});
	},



	/**
	 *
	*/
	_setTemplateCheckbox : function(obj)
	{
		var array = obj.now.split('-');
		for (var i = 0; i < obj.arr.length; i++) {
			var ele = $(document.createElement('li'));
			if (Prototype.Browser.IE) ele.addClassName('ie');
			else if (Prototype.Browser.Gecko) ele.addClassName('firefox');
			else if (navigator.userAgent.match("Chrome")) ele.addClassName('chrome');

			var eleTag = $(document.createElement('input'));
			eleTag.id = this.idSelf + obj.arr[i].id;
			eleTag.value = obj.arr[i].value;

			for (var j=0; j<array.length; j++) {
				if(array[j] == obj.arr[i].value) eleTag.selected = true;
			}
			ele.insert(obj.arr[i].strTitle);
			obj.eleInsert.insert(ele);
		}
	},

	/**
	 *
	*/
	_setTemplateContent : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if(obj.arr[i].flagContentUse) {
				$(this.idSelf + obj.arr[i].id).hide();
			}
		}
	},

	/**
	 *
	*/
	getValue : function()
	{
		return this.vars.varsDetail;
	},

	/**
	 *
	*/
	getFormValue : function()
	{
		var data = this._getFormValue({arr : this.vars.varsDetail});

		return data;
	},

	/**
	 *
	*/
	_getFormValue : function(obj)
	{
		var data = {};
		for (var i = 0; i < obj.arr.length; i++) {
			data[obj.arr[i].id] = obj.arr[i].value;
		}

		return data;
	},

	/**
	 *
	*/
	setValue : function()
	{
		this._setValue({arr : this.vars.varsDetail});
	},

	/**
	 *
	*/
	_setValue : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].flagCommentUse) continue;
			if (obj.arr[i].flagTag == 'select' && obj.arr[i].flagMultiple) {
				var ele  = $(this.idSelf + obj.arr[i].id);
				var data = {};
				var flagSelect = 0;
				for (var j = 0; j < ele.options.length; j++) {
					var option = ele.options[j];
					if (!flagSelect) {
						flagSelect = (option.selected)? 1 : 0;
					}
					data[option.value] = (option.selected)? 1 : 0;
				}
				obj.arr[i].value = (flagSelect)? data : '';

			} else if (obj.arr[i].flagInputType == 'file' && obj.arr[i].flagMultiple) {
				var ele  = $(this.idSelf + obj.arr[i].id);
				var data = [];
				var files = ele.files;
			    for (var j = 0; j < files.length; j++) {
			    	data.push(files[j].name);
			    }
				obj.arr[i].value = data;

			} else {
				obj.arr[i].value = $(this.idSelf + obj.arr[i].id).value;
			}

		}
	},

	/**
	 *
	*/
	viewFormLock : function(obj)
	{
		this._viewFormLock({
			idTarget : obj.idTarget,
			flag     : obj.flag,
			arr      : this.vars.varsDetail
		});
	},

	/**
	 *
	*/
	_viewFormLock : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.idTarget == obj.arr[i].id) {
				if (obj.flag) $(this.idSelf + obj.arr[i].id).disabled = true;
				else $(this.idSelf + obj.arr[i].id).disabled = false;
				return;
			}
		}
	},

	/**
	 *
	*/
	viewForm : function(obj)
	{
		this._viewForm({
			idTarget    : obj.idTarget,
			flagHideNow : obj.flagHideNow,
			arr         : this.vars.varsDetail
		});
	},

	/**
	 *
	*/
	_viewForm : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.idTarget == obj.arr[i].id) {
				if (obj.flagHideNow) $(this.idSelf + obj.arr[i].id + 'Wrap').hide();
				else $(this.idSelf + obj.arr[i].id + 'Wrap').show();
				return;
			}

		}
	},

	/**
	 * 	idTarget : mix
	 * 	value    : mix
	*/
	setValueVars : function(obj)
	{
		if (obj.flag == 'selectMultiple') {
			this._setValueSelectMultiple({
				arr      : this.vars.varsDetail,
				value    : obj.value,
				idTarget : obj.idTarget,
			});

		} else {
			this._setValueVars({
				idTarget : obj.idTarget,
				value    : obj.value,
				arr      : this.vars.varsDetail
			});
		}


	},

	/**
	 *
	*/
	_setValueVars : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].flagCommentUse) continue;
			if (obj.idTarget == obj.arr[i].id) {
				$(this.idSelf + obj.arr[i].id).value = obj.value;
				obj.arr[i].value = obj.value;
				if (obj.arr[i].flagTag == 'select') {
					var ele  = $(this.idSelf + obj.arr[i].id);
					for (var j = 0; j < ele.options.length; j++) {
						ele.options[j].selected = false;
						if (ele.options[j].value == obj.value) {
							ele.options[j].selected = true;
						}
					}
				} else if(obj.arr[i].flagTag == 'textarea' && obj.arr[i].numMaxlength) {
					this._resetTextArea({
						vars      : obj.arr[i],
						ele       : $(this.idSelf + obj.arr[i].id),
						eleLength : $(this.idSelf + obj.arr[i].id + 'KeyWrap')
					});
				}
				return;
			}

		}
	},

	/**
	 *
	*/
	_setValueSelectMultiple : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].flagCommentUse) continue;
			if (obj.idTarget == obj.arr[i].id) {
				if (obj.arr[i].flagTag == 'select' && obj.arr[i].flagMultiple) {
					var ele  = $(this.idSelf + obj.arr[i].id);
					for (var j = 0; j < ele.options.length; j++) {
						if (parseFloat(obj.value[ele.options[j].value])) ele.options[j].selected = true;
						else ele.options[j].selected = false;
					}
					obj.arr[i].value = obj.value;
				}
			}
		}
	},

	/**
	 *
	*/
	checkValue : function()
	{
		var insCheck = new Code_Lib_CheckValue();
		this.vars.varsDetail = insCheck.checkValue({
			arr : this.vars.varsDetail
		});
	},

	/**
	 *
	*/
	resetValueError : function()
	{
		this._resetValueError({arr : this.vars.varsDetail});
	},

	/**
	 *
	*/
	_resetValueError : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].flagCommentUse) continue;
			$(this.idSelf + obj.arr[i].id + 'Wrap').down('.codeLibFormError', 0).hide();
			$(this.idSelf + obj.arr[i].id + 'Wrap').down('.codeLibFormError', 0).innerHTML = '';
		}
	},

	/**
	 *
	*/
	checkValueError : function()
	{
		return this._checkValueError({arr : this.vars.varsDetail});
	},

	/**
	 *
	*/
	_checkValueError : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].flagErrorNow) return 1;
		}
	},

	/**
	 *
	*/
	showValueError : function(obj)
	{
		this._showValueError({
			flagType : obj.flagType,
			arr      : this.vars.varsDetail
		});
		this.showBtnBottom();
	},

	/**
	 *
	*/
	_showValueError : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			var flag = this._getValueErrorComment({
				arr      : obj.arr[i].arrayError,
				id       : obj.arr[i].id,
				flagType : obj.flagType
			});
			if (flag) continue;
		}
	},


	/**
	 *
	*/
	_getValueErrorComment : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].flagNow) {
				var id = this.idSelf + obj.id + 'Wrap';
				$(id).down('.codeLibFormError', 0).show();
				$(id).down('.codeLibFormError', 0).insert(obj.arr[i].strComment[obj.flagType]);
				return 1;
			}
		}
	},

	/**
	 *
	*/
	showValueAttestError : function(obj)
	{
		this.resetValueError();
		this._showValueAttestError({
			arr      : this.vars.varsDetail,
			flagType : obj.flagType,
			str      : (obj.str)? obj.str : ''
		});
		this.showBtnBottom();
	},

	/**
	 *
	*/
	_showValueAttestError : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			this._getValueAttestErrorComment({
				arr      : obj.arr[i].arrayError,
				id       : obj.arr[i].id,
				flagType : obj.flagType,
				str      : obj.str
			});
		}
	},

	/**
	 *
	*/
	_getValueAttestErrorComment : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].flagUse && obj.arr[i].flagCheck == 'attest') {
				var id = this.idSelf + obj.id + 'Wrap';
				if (obj.str && (obj.arr[i].strComment[obj.flagType] || obj.arr[i].strComment[obj.flagType] === '')) {
					$(id).down('.codeLibFormError', 0).show();
					$(id).down('.codeLibFormError', 0).insert(obj.str);

				} else if (obj.str && (obj.flagType == undefined || obj.flagType == null)) {
					$(id).down('.codeLibFormError', 0).show();
					$(id).down('.codeLibFormError', 0).insert(obj.str);

				} else {
					if (obj.str) {
						$(id).down('.codeLibFormError', 0).show();
						$(id).down('.codeLibFormError', 0).insert(obj.str);
						continue;

					} else {
						$(id).down('.codeLibFormError', 0).show();
						$(id).down('.codeLibFormError', 0).insert(obj.arr[i].strComment[obj.flagType]);
					}
				}
				return;
			}
		}
	}

});
<?php }
}
?>