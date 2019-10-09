<?php /* Smarty version 3.1.24, created on 2016-08-20 07:30:29
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/thumbnail.js" */ ?>
<?php
/*%%SmartyHeaderCode:63811751657b807154baed7_28272740%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '586b3b0fcd3bbecdf8c8377a8a874138e959e5a4' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/thumbnail.js',
      1 => 1471523676,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '63811751657b807154baed7_28272740',
  'variables' => 
  array (
    'varsLoad' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_57b807154fe9f5_28255050',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_57b807154fe9f5_28255050')) {
function content_57b807154fe9f5_28255050 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '63811751657b807154baed7_28272740';
?>

/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_Thumbnail = Class.create({

	varsLoad : <?php echo $_smarty_tpl->tpl_vars['varsLoad']->value;?>
,

	/**
	 *
	*/
	initialize : function(obj)
	{
		this._iniAllot(obj);
		this.iniVars(obj);
		this.iniListener();
		this.iniWrap();
		this.iniFormat();
		this.iniLog();
		this.iniRelay();
		this.iniPage();
		this.iniBtnBottom();
	},

	/**
	 *
	*/
	iniReload : function()
	{
		this.stopListener();
		$(this.idSelf).remove();
		this.iniWrap();
		this.iniFormat();
		this.iniLog();
		this.iniRelay();
		this.iniPage();
		this.iniBtnBottom();
	},

	/**
	 *
	*/
	iniRelay : function()
	{
		if (!this.vars.varsStatus.flagRelayUse) return;
		this.varsRelay.numTop = this.insFormat.eleTemplate.body.offsetHeight;
		this.varsRelay.height = this.insFormat.eleTemplate.body.scrollHeight;
		this.templateRelayListener();
	},

	/**
	 *
	*/
	templateRelayListener : function()
	{
		this.insListener.set({
			flagType15 : 1, bindAsEvent : 1, insCurrent : this, event : 'scroll',
			strFunc : 'scrollRelay', ele : this.insFormat.eleTemplate.body, vars : ''
		});
	},

	/**
	 *
	*/
	varsRelay : { numTop : 0, numHeight : 0, num : 0 },
	scrollRelay : function()
	{
		var numTopNow = this.insFormat.eleTemplate.body.scrollTop + this.insFormat.eleTemplate.body.offsetHeight;
		if (this.varsRelay.numTop >= this.varsRelay.numHeight) return;
		if (numTopNow > this.varsRelay.numTop) this.varsRelay.numTop = numTopNow;
		this.loadRelay({
			arr : this.vars.varsDetail
		});
	},

	/**
	 *
	*/
	loadRelay : function(obj)
	{
		for (var i = this.varsRelay.num + 1; i < obj.arr.length; i++) {
			var ele = $(this.idSelf + 'Log' + i).down('.codeLibThumbnailLogImgWrap', 0);
			if (!obj.arr[i].varsThumbnailDetail.stampPath) {
				ele.innerHTML = '';
				var eleImg = $(document.createElement('span'));
				eleImg.addClassName('codeLibThumbnailLogImg');
				eleImg.title = obj.arr[i].varsThumbnailDetail.strTitle;
				eleImg.addClassName('codeLibThumbnailLogImg404');
				ele.insert(eleImg);
			} else {
				if (ele.offsetTop <= this.varsRelay.numTop) {
					var eleImg = $(document.createElement('img'));
					eleImg.addClassName('codeLibThumbnailLogImg');
					var str = '?';
					str += 'level=' + obj.arr[i].varsThumbnailDetail.strLevel + '&';
					str += 'type=' + obj.arr[i].varsThumbnailDetail.fileType + '&';
					str += 'stamp=' + obj.arr[i].varsThumbnailDetail.stampPath;
					eleImg.src = this.insRoot.vars.varsSystem.path.image + str;
					eleImg.addClassName('codeLibBaseCursorPointer');
					eleImg.title = obj.arr[i].varsThumbnailDetail.strTitle;
					this.insListener.set({
						flagType15 : 1, bindAsEvent : 0, insCurrent : this, event : 'load',
						strFunc : 'onloadRelay', ele : eleImg, vars : { ele : ele, eleImg : eleImg
					}});
					this.varsRelay.num = i;
				}
			}
		}
	},

	/**
	 *
	*/
	onloadRelay : function(obj)
	{
		obj.ele.innerHTML = '';
		obj.ele.insert(obj.eleImg);
	},

	/**
	 *
	*/
	eleWrap : null,
	iniWrap : function()
	{
		var ele = $(document.createElement('div'));
		ele.id = this.idSelf;
		ele.addClassName('codeLibThumbnailWrap');
		this.eleInsert.insert(ele);
		this.eleWrap = ele;
		this.eleWrap.style.width = this.getWrapWidth() + 'px';
		this.eleWrap.style.height = this.getWrapHeight() + 'px';
	},

	/**
	 *
	*/
	getWrapHeight : function()
	{
		var array = this.eleInsert.style.height.split('px');
		var data = parseFloat(array[0] - 1);

		return  data;
	},

	/**
	 *
	*/
	getWrapWidth : function(obj)
	{
		array = this.eleInsert.style.width.split('px');
		var data = parseFloat(array[0]);

		return  data;
	},

	/**
	 *
	*/
	removeWrap : function()
	{
		$(this.idSelf).remove();
		if (this.eleInsertBtnLeft) this.eleInsertBtnLeft.innerHTML = '';
		if (this.eleInsertBtnRight) this.eleInsertBtnRight.innerHTML = '';
	},

	/**
	 *
	*/
	eleInsert : null, insRoot : null, insCurrent : null, insSelf : null, idSelf : null,
	vars : null, eleInsertBtnLeft : null, eleInsertBtnRight : null,
	iniVars : function(obj)
	{
		this.eleInsertBtnLeft = obj.eleInsertBtnLeft;
		this.eleInsertBtnRight = obj.eleInsertBtnRight;
		this.eleInsert = obj.eleInsert;
		this.insRoot = obj.insRoot;
		this.insCurrent = obj.insCurrent;
		this.insSelf = this;
		this.idSelf = obj.idSelf;
		this.vars = (Object.toJSON(obj.vars)).evalJSON();
		this.iniBtnVars();
		this.allot({
			insCurrent : this.insCurrent,
			from       : 'iniVars'
		});
	},

	/**
	 *
	*/
	updateVars : function()
	{
		this.allot({
			insCurrent : this.insCurrent,
			from       : 'updateVars',
			vars   : {
				vars       : this.vars,
				varsSelect : this.varsBtn
			}
		});
	},

	/**
	 *
	*/
	insFormat : null,
	iniFormat : function()
	{
		this.templateFormat();
	},
	templateFormat : function(obj)
	{
		this.insFormat = new Code_Lib_TemplateFormat({
			eleInsert  : this.eleWrap,
			insRoot    : this.insRoot,
			insCurrent : this.insSelf,
			idSelf     : this.idSelf + 'Format',
			vars   : this.vars.varsFormat
		});
	},

	/**
	 *
	*/
	staticLog : {numMargin : 5, numHeight : 16, numHeightWrap : 127},
	varsLog : null,
	iniLog : function(obj)
	{
		if (this.vars.varsStatus.flagRelayUse) {
			this.varsLog = {
				loadTop : (this.varsRelay.numTop > this.insFormat.eleTemplate.body.offsetHeight)?
						  this.varsRelay.numTop
						: this.insFormat.eleTemplate.body.offsetHeight
			};
		}
		this.templateLog({
			arr : this.vars.varsDetail
		});
	},

	/**
	 *
	*/
	templateLog : function(obj)
	{
		var insDisplay = new Code_Lib_TimeDisplay();
		this.insFormat.eleTemplate.body.addClassName('codeLibThumbnailBodyWrap');
		for (var i = 0; i < obj.arr.length; i++) {
			var eleWrap = $(document.createElement('span'));
			eleWrap.addClassName('codeLibThumbnailLogWrap');
			eleWrap.id = this.idSelf + 'Log' + i;
			if (this.vars.varsStatus.flagBgUse) eleWrap.addClassName(obj.arr[i].strClassBg);
			this.insFormat.eleTemplate.body.insert(eleWrap);
			var numHeight = this.staticLog.numHeightWrap;
			if (this.vars.varsStatus.flagSizeUse) {
				numHeight += this.staticLog.numHeight;
			}
			if (this.vars.varsStatus.flagTimeLengthUse) {
				numHeight += this.staticLog.numHeight;
			}
			if (this.vars.varsStatus.flagTimeRegisterUse) {
				numHeight += this.staticLog.numHeight;
			}
			eleWrap.setStyle({
				height : numHeight + 'px'
			});

			var eleImgWrap = $(document.createElement('span'));
			eleImgWrap.addClassName('codeLibThumbnailLogImgWrap');
			eleImgWrap.addClassName('codeLibBaseCursorPointer');
			eleWrap.insert(eleImgWrap);
			if (!obj.arr[i].varsThumbnailDetail.stampPath) {
				var eleImg = $(document.createElement('span'));
				eleImg.addClassName('codeLibThumbnailLogImg');
				eleImg.title = obj.arr[i].varsThumbnailDetail.strTitle;
				eleImg.addClassName('codeLibThumbnailLogImg404');
				eleImgWrap.insert(eleImg);
			} else {
				if (this.vars.varsStatus.flagRelayUse) {
					if (eleWrap.offsetTop > this.varsLog.loadTop) {
						var eleImg = $(document.createElement('span'));
						eleImg.addClassName('codeLibThumbnailLogImgLoad');
						eleImg.addClassName('codeLibServerImgLoading');
						eleImgWrap.insert(eleImg);
					} else {
						var eleImg = $(document.createElement('img'));
						eleImg.addClassName('codeLibThumbnailLogImg');
						var str = '?';
						str += 'level=' + obj.arr[i].varsThumbnailDetail.strLevel + '&';
						str += 'type=' + obj.arr[i].varsThumbnailDetail.fileType + '&';
						str += 'stamp=' + obj.arr[i].varsThumbnailDetail.stampPath;
						eleImg.src = this.insRoot.vars.varsSystem.path.image + str;
						eleImg.addClassName('codeLibBaseCursorPointer');
						eleImg.title = obj.arr[i].varsThumbnailDetail.strTitle;
						eleImgWrap.insert(eleImg);
						this.varsRelay.num = i;
					}
				} else {
					var eleImg = $(document.createElement('img'));
					eleImg.addClassName('codeLibThumbnailLogImg');
					var str = '?';
					str += 'level=' + obj.arr[i].varsThumbnailDetail.strLevel + '&';
					str += 'type=' + obj.arr[i].varsThumbnailDetail.fileType + '&';
					str += 'stamp=' + obj.arr[i].varsThumbnailDetail.stampPath;
					eleImg.src = this.insRoot.vars.varsSystem.path.image + str;
					eleImg.addClassName('codeLibBaseCursorPointer');
					eleImg.title = obj.arr[i].varsThumbnailDetail.strTitle;
					eleImgWrap.insert(eleImg);
					this.varsRelay.num = i;
				}
			}

			var eleDetailWrap = $(document.createElement('span'));
			eleDetailWrap.addClassName('codeLibThumbnailLogDetailWrap');
			eleWrap.insert(eleDetailWrap);

			var eleTitleWrap = $(document.createElement('span'));
			eleTitleWrap.addClassName('codeLibThumbnailLogTitleWrap');
			eleDetailWrap.insert(eleTitleWrap);

			var eleTitleImg = $(document.createElement('span'));
			eleTitleImg.addClassName(obj.arr[i].strClass);
			eleTitleImg.addClassName('codeLibThumbnailLogBlock');
			if (this.vars.varsStatus.flagMoveUse) {
				eleTitleImg.addClassName('codeLibThumbnailLogMove');
				eleTitleImg.addClassName('codeLibBaseCursorMove');
			}
			eleTitleWrap.insert(eleTitleImg);

			var eleTitle = $(document.createElement('span'));
			eleTitle.addClassName('codeLibThumbnailLogTitle');
			eleTitle.addClassName('codeLibBaseMarginLeftFive');
			eleTitle.addClassName('codeLibBaseCursorPointer');
			eleTitle.title = obj.arr[i].varsThumbnailDetail.strTitle;
			var strTitle = obj.arr[i].varsThumbnailDetail.fileType
						+ '  : ' + obj.arr[i].varsThumbnailDetail.strTitle;
			var str = (obj.arr[i].varsThumbnailDetail.fileType)?
					  strTitle
					: obj.arr[i].varsThumbnailDetail.strTitle;
			eleTitle.insert(str);
			if (this.vars.varsStatus.flagFontUse) {
				eleTitle.addClassName(obj.arr[i].strClassFont);
			}
			if (this.vars.varsStatus.flagBoldUse && obj.arr[i].flagBoldNow) {
				eleTitle.addClassName('codeLibBaseFontBold');
			}
			eleTitleWrap.insert(eleTitle);
			if (this.vars.varsStatus.flagTimeRegisterUse) {
				var eleTimeRegister = $(document.createElement('span'));
				eleTimeRegister.addClassName('codeLibBaseCursorPointer');
				eleTimeRegister.addClassName('codeLibThumbnailLogTimeRegister');
				var objTime = this.insRoot.insTimeZone.adjustDate({
					stamp : obj.arr[i].varsThumbnailDetail.stampRegister * 1000
				});
				var strTime = insDisplay.get({
					flagType : 3,
					vars : objTime
				});
				var str = strTime;
				eleTimeRegister.insert(str);
				if (this.vars.varsStatus.flagFontUse) {
					eleTimeRegister.addClassName(obj.arr[i].strClassFont);
				}
				if (this.vars.varsStatus.flagBoldUse && obj.arr[i].flagBoldNow) {
					eleTimeRegister.addClassName('codeLibBaseFontBold');
				}
				eleDetailWrap.insert(eleTimeRegister);
			}
			if (this.vars.varsStatus.flagTimeLengthUse) {
				var eleTimeLength = $(document.createElement('span'));
				eleTimeLength.addClassName('codeLibBaseCursorPointer');
				eleTimeLength.addClassName('codeLibThumbnailLogTimeLength');
				var str = this.varsLoad.varsWhole.strTimeLength
						+ '  : ' + obj.arr[i].varsThumbnailDetail.numTimeLength;
				eleTimeLength.insert(str);
				if (this.vars.varsStatus.flagFontUse) {
					eleTimeLength.addClassName(obj.arr[i].strClassFont);
				}
				if (this.vars.varsStatus.flagBoldUse && obj.arr[i].flagBoldNow) {
					eleTimeLength.addClassName('codeLibBaseFontBold');
				}
				eleDetailWrap.insert(eleTimeLength);
			}
			if (this.vars.varsStatus.flagTimeLengthUse) {
				var eleSize = $(document.createElement('span'));
				eleSize.addClassName('codeLibThumbnailLogSize');
				eleSize.addClassName('codeLibBaseCursorPointer');
				var str = this.varsLoad.varsWhole.strSize
						+ '  : ' + obj.arr[i].varsThumbnailDetail.numSize;
				eleSize.insert(str);
				if (this.vars.varsStatus.flagFontUse) {
					eleSize.addClassName(obj.arr[i].strClassFont);
				}
				if (this.vars.varsStatus.flagBoldUse && obj.arr[i].flagBoldNow) {
					eleSize.addClassName('codeLibBaseFontBold');
				}
				eleDetailWrap.insert(eleSize);
			}
			this.templateLogMoveListener({
				ele      : eleTitleImg,
				eleWrap  : eleWrap,
				vars : obj.arr[i]
			});
			this.templateBtnListener({
				ele      : eleWrap,
				vars : obj.arr[i]
			});
		}
	},

	/**
	 *
	*/
	templateLogMoveListener : function(obj)
	{
		if (!this.vars.varsStatus.flagMoveUse || !obj.vars.flagMoveUse) return;
		this.insListener.set({
			bindAsEvent : 1, insCurrent : this, event : 'mousedown',
			strFunc : 'mousedownMove', ele : obj.ele,
			vars : { vars : obj.vars, ele : obj.eleWrap }
		});
	},

	/**
	 *
	*/
	templateMoveListener : function(obj)
	{
		this.insListener.set({
			bindAsEvent : 1, insCurrent : this, event : 'mousemove',
			strFunc : 'mousemoveMove', ele : document, vars : ''
		});
		this.insListener.set({
			bindAsEvent : 1, insCurrent : this, event : 'mouseup',
			strFunc : 'mouseupMove', ele : document, vars : ''
		});
	},

	/**
	 *
	*/
	varsMove : {},
	mousedownMove : function(evt, obj)
	{
		this.templateMoveListener();
		this.varsMove = {};
		this.varsMove = {
			flag     : 1,
			ele      : evt.element(),
			vars : obj,
			eleNavi  : null
		};
		this.templateMoveNavi({
			vars : obj,
			evt      : evt
		});
		this.allot({
			insCurrent : this.insCurrent,
			from       : 'mousedownMove',
			vars   : obj.vars
		});
	},

	/**
	 *
	*/
	templateMoveNavi : function(obj)
	{
		var zIndex = this.insRoot.setZIndex();
		var ele = obj.vars.ele.cloneNode(true);
		$(this.insRoot.vars.varsSystem.id.root).insert(ele);
		this.varsMove.eleNavi = ele;
		ele.addClassName('codeLibThumbnailNavi');
		ele.setStyle({
			left   : (obj.evt.pointerX() + this.staticMove.numNaviLeft) + 'px',
			top    : (obj.evt.pointerY() + this.staticMove.numNaviTop) + 'px',
			zIndex : zIndex
		});
	},

	/**
	 *
	*/
	staticMove : {numNaviLeft : 15, numNaviTop : 5},
	mousemoveMove : function(evt, obj)
	{
		if (!this.varsMove.flag) return;
		if (obj) evt.stop();
		else obj = evt;
		this.varsMove.eleNavi.setStyle({
			top : (evt.pointerY() + this.staticMove.numNaviTop) + 'px',
			left : (evt.pointerX() + this.staticMove.numNaviLeft) + 'px'
		});
	},

	/**
	 *
	*/
	mouseupMove : function(evt, obj)
	{
		if (obj) evt.stop();
		else obj = evt;
		if (!this.varsMove.flag) return;
		this.allot({
			insCurrent : this.insCurrent,
			from       : 'mouseupMove',
			vars   : this.varsMove.vars
		});
		this.varsMove.eleNavi.remove();
		this.varsMove = {};
	},

	/**
	 *
	*/
	iniBtnVars : function()
	{
		this.varsBtn = [];
	},

	/**
	 *
	*/
	varsBtn:[],
	templateBtnListener : function(obj)
	{
		if (!obj.vars.flagBtnUse) return;

		this.insListener.set({
			bindAsEvent : 1, insCurrent : this, event : 'mousedown',
			strFunc : 'mousedownBtn', ele : obj.ele, vars : { vars : obj.vars, ele : obj.ele }
		});
		this.insListener.set({
			bindAsEvent : 1, insCurrent : this, event : 'dblclick',
			strFunc : 'dblclickBtn', ele : obj.ele, vars : { vars : obj.vars }
		});

		var cut = this.vars.varsStatus;
		this.templateBtnListenerChild({
			ele : obj.ele.down('.codeLibThumbnailLogTitle', 0)
		});
		if (cut.flagTimeRegisterUse) {
			this.templateBtnListenerChild({
				ele : obj.ele.down('.codeLibThumbnailLogTimeRegister', 0)
			});
		}
		if (cut.flagTimeLengthUse) {
			this.templateBtnListenerChild({ele : obj.ele.down('.codeLibThumbnailLogTimeLength', 0)});
		}
		if (cut.flagSizeUse) {
			this.templateBtnListenerChild({ele : obj.ele.down('.codeLibThumbnailLogSize', 0)});
		}
	},

	/**
	 *
	*/
	templateBtnListenerChild : function(obj)
	{
		this.insListener.set({
			bindAsEvent : 0, insCurrent : this, event : 'mouseover',
			strFunc : 'mouseoverBtn', ele : obj.ele, vars : { ele : obj.ele }
		});
		this.insListener.set({
			bindAsEvent : 0, insCurrent : this, event : 'mouseout',
			strFunc : 'mouseoutBtn', ele : obj.ele, vars : { ele : obj.ele }
		});
	},

	/**
	 *
	*/
	dblclickBtn : function(evt, obj)
	{
		if (obj) evt.stop();
		else obj = evt;
		this.allot({
			insCurrent : this.insCurrent,
			from       : 'dblclickBtn',
			vars   : obj.vars
		});
	},

	/**
	 *
	*/
	mousedownBtn : function(evt, obj)
	{
		if (obj) evt.stop();
		else obj = evt;
		this.varsBtn = [];
		this.varsBtn.push(obj);
		this.iniBtnSelect({
			ele      : obj.ele,
			vars : obj.vars
		});
		this.allot({
			insCurrent : this.insCurrent,
			from       : 'mousedownBtn',
			vars   : obj.vars
		});
	},

	/**
	 *
	*/
	mouseoverBtn : function(obj)
	{
		obj.ele.addClassName('codeLibBaseUnderline');
	},

	/**
	 *
	*/
	mouseoutBtn : function(obj)
	{
		obj.ele.removeClassName('codeLibBaseUnderline');
	},

	/**
	 *
	*/
	iniBtnSelect : function(obj)
	{
		if (!this.varsBtn) return;
		this.setBtnSelect({
			arr      : this.vars.varsDetail,
			ele      : obj.ele,
			vars : obj.vars
		});
		this.removeBtnSelectBold({
			ele      : obj.ele,
			vars : obj.vars
		});
	},

	/**
	 *
	*/
	setBtnSelect : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			var id = this.idSelf + 'Log' + i;
			$(id).removeClassName('codeLibScheduleSelect');
		}
		obj.ele.addClassName('codeLibScheduleSelect');
	},

	/**
	 *
	*/
	removeBtnSelectBold : function(obj)
	{
		if (!this.vars.varsStatus.flagBoldUse || !obj.vars.flagBoldNow) return;
		obj.vars.flagBoldNow = 0;
		this.updateVars();
		obj.ele.down('.codeLibThumbnailLogTitle',0).removeClassName('codeLibBaseFontBold');
		var cut = this.vars.varsStatus;
		if (cut.flagTimeRegisterUse) {
			obj.ele.down('.codeLibThumbnailLogTimeRegister',0).removeClassName('codeLibBaseFontBold');
		}
		if (cut.flagTimeLengthUse) {
			obj.ele.down('.codeLibThumbnailLogTimeLength',0).removeClassName('codeLibBaseFontBold');
		}
		if (cut.flagSizeUse) {
			obj.ele.down('.codeLibThumbnailLogSize',0).removeClassName('codeLibBaseFontBold');
		}
	},

	/**
	 *
	*/
	staticBtnBottom : {numMargin : 5},
	iniBtnBottom : function()
	{
		if (!this.vars.varsStatus.flagBtnBottomUse) return;
		if (!this.eleInsertBtnLeft && this.eleInsertBtnRight) return;

		if (this.eleInsertBtnLeft) {
			this.eleInsertBtnLeft.innerHTML = '';
			this.eleInsertBtnLeft.style.marginTop = this.staticBtnBottom.numMargin + 'px';
		}
		if (this.eleInsertBtnRight) {
			this.eleInsertBtnRight.innerHTML = '';
			this.eleInsertBtnRight.style.marginTop = this.staticBtnBottom.numMargin + 'px';
		}
		this.templateBtnBottom({
			arr : this.vars.varsBtn
		});
	},

	/**
	 *
	*/
	templateBtnBottom : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].flagUse) continue;
			if (obj.arr[i].flagBtnUse) this.templateBtnBottomBtn({vars : obj.arr[i]});
		}
	},

	/**
	 *
	*/
	templateBtnBottomBtn : function(obj)
	{
		var insBtn = new Code_Lib_Btn();
		insBtn.iniBtn({
			eleInsert  : (obj.vars.flagLeftUse)? this.eleInsertBtnLeft : this.eleInsertBtnRight,
			id         : this.idSelf + 'BtnBottom' + obj.vars.id,
			strFunc    : obj.vars.strFunc,
			strTitle   : obj.vars.strTitle,
			insCurrent : this.insCurrent,
			vars   : obj.vars
		});
		this._setListener({ins : insBtn});
		$(this.idSelf + 'BtnBottom' + obj.vars.id).style.marginRight = this.staticBtnBottom.numMargin + 'px';
	},

	/**
	 *
	*/
	removeBtnBottom : function()
	{
		if (this.eleInsertBtnLeft) this.eleInsertBtnLeft.innerHTML = '';
		if (this.eleInsertBtnRight) this.eleInsertBtnRight.innerHTML = '';
	},

	/**
	 *
	*/
	insPage : null,
	iniPage : function()
	{
		if (!this.vars.varsStatus.flagPageUse) return;
		this.templatePage();
	},

	/**
	 *
	*/
	templatePage : function()
	{
		this.insPage = new Code_Lib_BtnPage({
			eleInsertBtnLeft : (this.vars.varsStatus.flagInnerPageUse)?
							  this.insFormat.eleTemplate.fooder.down('.codeLibBaseMarginLeftFive', 0)
							: this.eleInsertBtnLeft ,
			eleInsertBtnRight : (this.vars.varsStatus.flagInnerPageUse)?
							  this.insFormat.eleTemplate.fooder.down('.codeLibBaseMarginRightFive',0)
							: this.eleInsertBtnRight,
			insRoot          : this.insRoot,
			insCurrent       : this.insSelf,
			idSelf           : this.idSelf + 'Page',
			allot            : this.getPageAllot(),
			vars         : this.vars.varsPage
		});
	},

	/**
	 *
	*/
	getPageAllot : function()
	{
		var allot =  function(obj)
		{
			var insCurrent = obj.insCurrent;
			if (obj.from == 'checkPage') {
				insCurrent.allot({
					insCurrent : insCurrent.insCurrent,
					from       : 'checkPage',
					vars   : obj.vars
				});
			}
		};

		return allot;
	},

	/**
	 *
	*/
	removePage : function()
	{
		if (this.vars.varsStatus.flagInnerPageUse) {
			this.insFormat.eleTemplate.fooder.down('.codeLibBaseMarginLeftFive',0).innerHTML = '';
			this.insFormat.eleTemplate.fooder.down('.codeLibBaseMarginRightFive',0).innerHTML = '';
		} else {
			if (this.eleInsertBtnLeft) this.eleInsertBtnLeft.innerHTML = '';
			if (this.eleInsertBtnRight) this.eleInsertBtnRight.innerHTML = '';
		}
	},

	/**
	 *
	*/
	insListener : null,
	iniListener : function()
	{
		this.insListener = new Code_Lib_Listener();
				this.varsListener = [];
	},

	/**
	 *
	*/
	varsListener : null,
	setListener : function(obj)
	{
		var data = {ins : obj.ins};
		this.varsListener.push(data);
	},

	/**
	 *
	*/
	stopListener : function()
	{
		this.insListener.stop();
		this.stopListenerChild({
			arr : this.varsListener
		});
		if (this.insPage) this.insPage.stopListener();
		this.resetListener();
	},

	/**
	 *
	*/
	stopListenerChild : function(obj)
	{
		if (obj.arr.length == 0) return;
		for (var i = 0; i < obj.arr.length; i++) {
			obj.arr[i].ins.insListener.stop();
		}
	},

	/**
	 *
	*/
	resetListener : function()
	{
		this.varsListener = [];
	},

	/**
	 *
	*/
	allot : {},
	_iniAllot : function(obj)
	{
		this.allot = obj.allot;
	}
});
<?php }
}
?>