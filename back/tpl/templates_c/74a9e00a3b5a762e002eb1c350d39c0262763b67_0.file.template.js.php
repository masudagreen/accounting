<?php /* Smarty version 3.1.24, created on 2019-06-16 09:02:08
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/template.js" */ ?>
<?php
/*%%SmartyHeaderCode:14662561195d060590169760_76231091%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '74a9e00a3b5a762e002eb1c350d39c0262763b67' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/template.js',
      1 => 1560675140,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '14662561195d060590169760_76231091',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_5d06059016f449_40847998',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_5d06059016f449_40847998')) {
function content_5d06059016f449_40847998 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '14662561195d060590169760_76231091';
?>

/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_Template = Class.create({

	/**
	 *
	*/
	get : function(obj)
	{
		var tmplstr;
		if (obj.flagType == 'normalBox') {
			obj.numWrapWidth = obj.numWidth + 6;
			tmplstr='<div class="codeLibTemplateNormalBox" id="#{id}" style="width : #{numWrapWidth}px;">';
					tmplstr += '<div class="codeLibTemplateNormalBoxTop clearfix">';
						tmplstr += '<span class="codeLibTemplateNormalBoxTopLeft unselect"></span>';
						tmplstr += '<span class="codeLibTemplateNormalBoxTopMiddle unselect" style="width : #{numWidth}px;"></span>';
						tmplstr += '<span class="codeLibTemplateNormalBoxTopRight unselect"></span>';
					tmplstr += '</div>';
					tmplstr += '<div class="codeLibTemplateNormalBoxMiddle clearfix">';
						tmplstr += '<span class="codeLibTemplateNormalBoxMiddleLeft unselect" style="height : #{numHeight}px;" ></span>';
						tmplstr += '<span class="codeLibTemplateNormalBoxMiddleMiddle" style="width : #{numWidth}px; height : #{numHeight}px;" ></span>';
						tmplstr += '<span class="codeLibTemplateNormalBoxMiddleRight unselect" style="height : #{numHeight}px;" ></span>';
					tmplstr += '</div>';
					tmplstr += '<div class="codeLibTemplateNormalBoxBottom clearfix">';
						tmplstr += '<span class="codeLibTemplateNormalBoxBottomLeft unselect"></span>';
						tmplstr += '<span class="codeLibTemplateNormalBoxBottomMiddle unselect" style="width : #{numWidth}px;"></span>';
						tmplstr += '<span class="codeLibTemplateNormalBoxBottomRight unselect"></span>';
					tmplstr += '</div>';
			tmplstr += '</div>';
		}
		else if (obj.flagType == 'shadowBox') {
			obj.numWrapWidth = obj.numWidth + 12;
			tmplstr='<div class="codeLibTemplateShadowBox" id="#{id}" style="width : #{numWrapWidth}px;">';
					tmplstr += '<div class="codeLibTemplateShadowBoxTop clearfix">';
						tmplstr += '<span class="codeLibTemplateShadowBoxTopLeft unselect"></span>';
						tmplstr += '<span class="codeLibTemplateShadowBoxTopMiddle unselect" style="width : #{numWidth}px;"></span>';
						tmplstr += '<span class="codeLibTemplateShadowBoxTopRight unselect"></span>';
					tmplstr += '</div>';
					tmplstr += '<div class="codeLibTemplateShadowBoxMiddle clearfix">';
						tmplstr += '<span class="codeLibTemplateShadowBoxMiddleLeft unselect" style="height : #{numHeight}px;" ></span>';
						tmplstr += '<span class="codeLibTemplateShadowBoxMiddleMiddle" style="width : #{numWidth}px; height : #{numHeight}px;" ></span>';
						tmplstr += '<span class="codeLibTemplateShadowBoxMiddleRight unselect" style="height : #{numHeight}px;" ></span>';
					tmplstr += '</div>';
					tmplstr += '<div class="codeLibTemplateShadowBoxBottom clearfix">';
						tmplstr += '<span class="codeLibTemplateShadowBoxBottomLeft unselect"></span>';
						tmplstr += '<span class="codeLibTemplateShadowBoxBottomMiddle unselect" style="width : #{numWidth}px;"></span>';
						tmplstr += '<span class="codeLibTemplateShadowBoxBottomRight unselect"></span>';
					tmplstr += '</div>';
			tmplstr += '</div>';
		}
		else if (obj.flagType == 'menuBox') {
			obj.numWrapWidth = obj.numWidth + 12;
			tmplstr='<div class="codeLibTemplateMenuBox" id="#{id}" style="width : #{numWrapWidth}px;">';
					tmplstr += '<div class="codeLibTemplateMenuBoxTop clearfix">';
						tmplstr += '<span class="codeLibTemplateMenuBoxTopLeft unselect"></span>';
						tmplstr += '<span class="codeLibTemplateMenuBoxTopMiddle unselect" style="width : #{numWidth}px;"></span>';
						tmplstr += '<span class="codeLibTemplateMenuBoxTopRight unselect"></span>';
					tmplstr += '</div>';
					tmplstr += '<div class="codeLibTemplateMenuBoxMiddle clearfix">';
						tmplstr += '<span class="codeLibTemplateMenuBoxMiddleLeft unselect" style="height : #{numHeight}px;" ></span>';
						tmplstr += '<span class="codeLibTemplateMenuBoxMiddleMiddle" style="width : #{numWidth}px; height : #{numHeight}px;" ></span>';
						tmplstr += '<span class="codeLibTemplateMenuBoxMiddleRight unselect" style="height : #{numHeight}px;" ></span>';
					tmplstr += '</div>';
					tmplstr += '<div class="codeLibTemplateMenuBoxBottom clearfix">';
						tmplstr += '<span class="codeLibTemplateMenuBoxBottomLeft unselect"></span>';
						tmplstr += '<span class="codeLibTemplateMenuBoxBottomMiddle unselect" style="width : #{numWidth}px;"></span>';
						tmplstr += '<span class="codeLibTemplateMenuBoxBottomRight unselect"></span>';
					tmplstr += '</div>';
			tmplstr += '</div>';
		}

		else if (obj.flagType == 'scheduleFormat') {

			var fooder = 30;
			var separate = 5;
			var numIdle = 2;
			obj.headerWidth = obj.numWidth;
			obj.headerNaviHeight = 25;
			obj.headerGraduationHeight = 16;

			obj.numHeight = obj.numHeight - obj.headerNaviHeight- obj.headerGraduationHeight - separate*2 - fooder + numIdle;
			if (!obj.flagFooderUse) obj.numHeight = obj.numHeight + fooder;

			tmplstr='<div id="#{id}" class="codeLibTemplateListFormat">';
				tmplstr += '<div class="codeLibTemplateListFormatHeader">';
					tmplstr += '<div class="codeLibTemplateListFormatHeaderNavi">';
						tmplstr += '<div class="codeLibTemplateListFormatHeaderNaviWrap clearfix" style="width : #{headerWidth}px; height : #{headerNaviHeight}px;" ></div>';
						tmplstr += '<div class="codeLibTextLine"></div>';
						tmplstr += '<div class="codeLibTextDoubleLineIdle"></div>';
					tmplstr += '</div>';
					tmplstr += '<div class="codeLibTemplateListFormatHeaderGraduation">';
						tmplstr += '<div class="codeLibTemplateListFormatHeaderGraduationWrap clearfix" style="width : #{headerWidth}px; height : #{headerGraduationHeight}px;" ></div>';
						tmplstr += '<div class="codeLibTextLine"></div>';
						tmplstr += '<div class="codeLibTextDoubleLineIdle"></div>';
					tmplstr += '</div>';
				tmplstr += '</div>';

				tmplstr += '<div class="codeLibTemplateListFormatBody" style="width : #{numWidth}px; height : #{numHeight}px; overflow : auto;"></div>';

				if (obj.flagFooderUse) {
					tmplstr += '<div class="codeLibTemplateListFormatFooder">';
						tmplstr += '<div class="codeLibTextDoubleLineIdle"></div>';
						tmplstr += '<div class="codeLibTextLine"></div>';
						tmplstr += '<div class="codeLibTemplateListFormatFooderWrap clearfix">';
							tmplstr += '<div class="codeLibBaseFloatLeft">';
								tmplstr += '<ul>';
									tmplstr += '<li class="codeLibTemplateListFormatFooderStr codeLibBaseMarginLeftFive"></li>';
								tmplstr += '</ul>';
							tmplstr += '</div>';
							tmplstr += '<div class="codeLibBaseFloatRight">';
								tmplstr += '<ul>';
									tmplstr += '<li class="codeLibTemplateListFormatFooderStr codeLibBaseMarginRightFive"></li>';
								tmplstr += '</ul>';
							tmplstr += '</div>';
						tmplstr += '</div>';
					tmplstr += '</div>';
				}
			tmplstr += '</div>';
		}
		else if (obj.flagType == 'normalFormat') {
			var numMargin = 10;
			var header = 27;
			var fooder = 32;
			obj.numWidth = obj.numWidth - numMargin * 2;
			obj.numHeight = obj.numHeight - numMargin * 2  - header - fooder;
			if (!obj.flagFooderUse) obj.numHeight = obj.numHeight + fooder;
			this.varStyleNormalFormatWidthTitle(obj);

			tmplstr='<div id="#{id}" class="codeLibTemplateNormalFormat codeLibBaseMarginTen">';
				tmplstr += '<div class="codeLibTemplateNormalFormatHeader" >';
					tmplstr += '<div class="codeLibTemplateNormalFormatHeaderWrap clearfix">';
						if (obj.flagHeaderLeftUse) {
							tmplstr += '<div class="codeLibBaseFloatLeft">';
								tmplstr += '<ul>';
									if (!obj.strClassHeaderLeft) {
										if (!obj.flagHeaderRightWidth && !obj.flagHeaderLeftWidth) {
											tmplstr += '<li class="codeLibTemplateNormalFormatHeaderLeftWidthTitle codeLibTemplateNormalFormatHeaderStr codeLibBaseMarginLeftFive" title="#{strTitleHeaderLeft}" >#{strTitleHeaderLeft}</li>';
										}else {
											tmplstr += '<li class="codeLibTemplateNormalFormatHeaderLeftWidthTitle codeLibTemplateNormalFormatHeaderStr codeLibBaseMarginLeftFive" title="#{strTitleHeaderLeft}" style="width : #{numWidthHeaderLeft}px;">#{strTitleHeaderLeft}</li>';
										}
									}
									else {
										if (!obj.flagHeaderRightWidth && !obj.flagHeaderLeftWidth) {
											tmplstr += '<li class="codeLibTemplateNormalFormatHeaderLeftWidthTitle codeLibTemplateNormalFormatHeaderImg codeLibBaseMarginLeftFive #{strClassHeaderLeft}" title="#{strTitleHeaderLeft}"  >#{strTitleHeaderLeft}</li>';
										}else {
											tmplstr += '<li class="codeLibTemplateNormalFormatHeaderLeftWidthTitle codeLibTemplateNormalFormatHeaderImg codeLibBaseMarginLeftFive #{strClassHeaderLeft}" title="#{strTitleHeaderLeft}" style="width : #{numWidthHeaderLeft}px; ">#{strTitleHeaderLeft}</li>';
										}
									}
								tmplstr += '</ul>';
							tmplstr += '</div>';
						}
						if (obj.flagHeaderRightUse) {
							tmplstr += '<div class="codeLibBaseFloatRight">';
								tmplstr += '<ul>';
									if (!obj.strClassHeaderRight) {
										if (!obj.flagHeaderRightWidth && !obj.flagHeaderLeftWidth) {
											tmplstr += '<li class="codeLibTemplateNormalFormatHeaderRightWidthTitle codeLibTemplateNormalFormatHeaderStr codeLibBaseMarginRightFive" title="#{strTitleHeaderRight}">#{strTitleHeaderRight}</li>';
										}else {
											tmplstr += '<li class="codeLibTemplateNormalFormatHeaderRightWidthTitle codeLibTemplateNormalFormatHeaderStr codeLibBaseMarginRightFive" title="#{strTitleHeaderRight}" style="width : #{numWidthHeaderRight}px;">#{strTitleHeaderRight}</li>';
										}
									}
									else {
										if (!obj.flagHeaderRightWidth && !obj.flagHeaderLeftWidth) {
											tmplstr += '<li class="codeLibTemplateNormalFormatHeaderRightWidthTitle codeLibBaseMarginRightFive #{strClassHeaderRight}" title="#{strTitleHeaderRight}">#{strTitleHeaderRight}</li>';
										}else {
											tmplstr += '<li class="codeLibTemplateNormalFormatHeaderRightWidthTitle codeLibBaseMarginRightFive #{strClassHeaderRight}" title="#{strTitleHeaderRight}" style="width : #{numWidthHeaderRight}px;">#{strTitleHeaderRight}</li>';
										}
									}
								tmplstr += '</ul>';
							tmplstr += '</div>';
						}
					tmplstr += '</div>';
					tmplstr += '<div class="codeLibTextDoubleLine"></div>';
					tmplstr += '<div class="codeLibTextDoubleLineIdle"></div>';
				tmplstr += '</div>';
				if (obj.flagBodyAutoUse) {tmplstr += '<div class="codeLibTemplateNormalFormatBody #{strClassBody}" style="width : #{numWidth}px; height : #{numHeight}px; overflow : auto;">#{strBody}</div>';}
				else {tmplstr += '<div class="codeLibTemplateNormalFormatBody #{strClassBody}" style="width : #{numWidth}px; height : #{numHeight}px; overflow : hidden;">#{strBody}</div>';}
				if (obj.flagFooderUse) {
					tmplstr += '<div class="codeLibTemplateNormalFormatFooder">';
						tmplstr += '<div class="codeLibTextDoubleLineIdle"></div>';
						tmplstr += '<div class="codeLibTextDoubleLine"></div>';
						tmplstr += '<div class="codeLibTemplateNormalFormatFooderWrap clearfix " style="width : #{numWidth}px; height : ' + fooder + 'px; overflow : hidden;">';
							if (obj.flagFooderLeftUse) {
								tmplstr += '<div class="codeLibBaseFloatLeft">';
									tmplstr += '<ul>';
										if (!obj.strClassFooderLeft) {
											if (!obj.flagFooderRightWidth && !obj.flagFooderLeftWidth) {
												tmplstr += '<li class="codeLibTemplateNormalFormatFooderLeftWidthTitle codeLibTemplateNormalFormatFooderStr codeLibBaseMarginLeftFive">#{strTitleFooderLeft}</li>';
											}else {
												tmplstr += '<li class="codeLibTemplateNormalFormatFooderLeftWidthTitle codeLibTemplateNormalFormatFooderStr codeLibBaseMarginLeftFive" style="width : #{numWidthFooderLeft}px;">#{strTitleFooderLeft}</li>';
											}
										}
										else {
											if (!obj.flagFooderRightWidth && !obj.flagFooderLeftWidth) {
												tmplstr += '<li class="codeLibTemplateNormalFormatFooderLeftWidthTitle codeLibTemplateNormalFormatFooderImg codeLibBaseMarginLeftFive #{strClassFooderLeft}"">#{strTitleFooderLeft}</li>';
											}else {
												tmplstr += '<li class="codeLibTemplateNormalFormatFooderLeftWidthTitle codeLibTemplateNormalFormatFooderImg codeLibBaseMarginLeftFive #{strClassFooderLeft}" style="width : #{numWidthFooderLeft}px;">#{strTitleFooderLeft}</li>';
											}
										}
									tmplstr += '</ul>';
								tmplstr += '</div>';
							}
							if (obj.flagFooderRightUse) {
								tmplstr += '<div class="codeLibBaseFloatRight">';
									tmplstr += '<ul>';
										if (!obj.strClassFooderRight) {
											if (!obj.flagFooderRightWidth && !obj.flagFooderLeftWidth) {
												tmplstr += '<li class="codeLibTemplateNormalFormatFooderRightWidthTitle codeLibTemplateNormalFormatFooderStr codeLibBaseMarginRightFive">#{strTitleFooderRight}</li>';
											}else {
												tmplstr += '<li class="codeLibTemplateNormalFormatFooderRightWidthTitle codeLibTemplateNormalFormatFooderStr codeLibBaseMarginRightFive" style="width : #{numWidthFooderRight}px;">#{strTitleFooderRight}</li>';
											}
										}
										else {
											if (!obj.flagFooderRightWidth && !obj.flagFooderLeftWidth) {
												tmplstr += '<li class="codeLibTemplateNormalFormatFooderRightWidthTitle codeLibTemplateNormalFormatFooderImg codeLibBaseMarginRightFive #{strClassFooderRight}">#{strTitleFooderRight}</li>';
											}else {
												tmplstr += '<li class="codeLibTemplateNormalFormatFooderRightWidthTitle codeLibTemplateNormalFormatFooderImg codeLibBaseMarginRightFive #{strClassFooderRight}" style="width : #{numWidthFooderRight}px;">#{strTitleFooderRight}</li>';
											}
										}
									tmplstr += '</ul>';
								tmplstr += '</div>';
							}
						tmplstr += '</div>';
					tmplstr += '</div>';
				}
			tmplstr += '</div>';
		} else if (obj.flagType == 'circleBox') {
			var idle = 10;
			var point = 1;
			var eleBtnWrap = $(document.createElement('span'));
			eleBtnWrap.addClassName('codeLibTemplateCircleBoxWrap');
			eleBtnWrap.id = obj.id;
			eleBtnWrap.addClassName('codeLibBaseCursorDefault');
			eleBtnWrap.addClassName('unselect');
			eleBtnWrap.unselectable = 'on';
			var eleBtnMiddle = $(document.createElement('span'));
			eleBtnMiddle.addClassName('codeLibTemplateCircleBoxMiddle');
			if (obj.strClass) {
				var eleClass = $(document.createElement('span'));
				eleClass.addClassName(obj.strClass);
				eleBtnWrap.title = obj.strTitle;
				eleBtnMiddle.insert(eleClass);
			} else {
				eleBtnMiddle.insert(obj.strTitle);
				$(this.insRoot.vars.varsSystem.id.root).insert(eleBtnMiddle);
			}
			var numWidth = (obj.numWidth)? obj.numWidth : eleBtnMiddle.offsetWidth + idle * 2;
			var eleBtnTop = $(document.createElement('span'));
			eleBtnTop.addClassName('codeLibTemplateCircleBoxTop');
			eleBtnWrap.insert(eleBtnTop);
			eleBtnTop.setStyle({
				width           : (numWidth - point * 2) + 'px',
				backgroundColor : obj.bgColor
			});
			eleBtnWrap.insert(eleBtnMiddle);
			eleBtnMiddle.setStyle({
				width           : numWidth + 'px',
				backgroundColor : obj.bgColor
			});
			var eleBtnBottom = $(document.createElement('span'));
			eleBtnBottom.addClassName('codeLibTemplateCircleBoxBottom');
			eleBtnWrap.insert(eleBtnBottom);
			eleBtnBottom.setStyle({
				width           : (numWidth - point * 2) + 'px',
				backgroundColor : obj.bgColor
			});
			eleBtnWrap.setStyle({
				width : numWidth + 'px'
			});
			return eleBtnWrap;
		} else if (obj.flagType == 'singleFormat') {

			var numMargin = 0;
			var header = 25;
			var fooder = 30;
			obj.numWidth = obj.numWidth - numMargin * 2;
			obj.numHeight = obj.numHeight - numMargin * 2  - header - fooder;
			if (!obj.flagHeaderUse) obj.numHeight = obj.numHeight + header;
			if (!obj.flagFooderUse) obj.numHeight = obj.numHeight + fooder;

			tmplstr='<div id="#{id}" class="codeLibTemplateListFormat">';
				if (obj.flagHeaderUse) {
					tmplstr += '<div class="codeLibTemplateListFormatHeader">';
						tmplstr += '<div class="codeLibTemplateListFormatHeaderWrap clearfix">';
							if (obj.flagHeaderLeftUse) {
								tmplstr += '<div class="codeLibBaseFloatLeft">';
									tmplstr += '<ul>';
										if (!obj.strClassHeaderLeft) {tmplstr += '<li class="codeLibTemplateListFormatHeaderStr codeLibBaseMarginLeftFive">#{strTitleHeaderLeft}</li>';}
										else {tmplstr += '<li class="codeLibTemplateListFormatHeaderImg codeLibBaseMarginLeftFive #{strClassHeaderLeft}">#{strTitleHeaderLeft}</li>';}
									tmplstr += '</ul>';
								tmplstr += '</div>';
							}
							if (obj.flagHeaderRightUse) {
								tmplstr += '<div class="codeLibBaseFloatRight">';
									tmplstr += '<ul>';
										if (!obj.strClassHeaderRight) {tmplstr += '<li class="codeLibTemplateListFormatHeaderStr codeLibBaseMarginRightFive">#{strTitleHeaderRight}</li>';}
										else {tmplstr += '<li class="codeLibTemplateListFormatHeaderImg codeLibBaseMarginRightFive #{strClassHeaderRight}" style="margin-top: 4px; height: 14px;" >#{strTitleHeaderRight}</li>';}
									tmplstr += '</ul>';
								tmplstr += '</div>';
							}
						tmplstr += '</div>';
						tmplstr += '<div class="codeLibTextLine"></div>';
						tmplstr += '<div class="codeLibTextDoubleLineIdle"></div>';
					tmplstr += '</div>';
				}
				if (obj.flagBodyAutoUse) {tmplstr += '<div class="codeLibTemplateListFormatBody #{strClassBody}" style="width : #{numWidth}px; height : #{numHeight}px; overflow : auto;">#{strBody}</div>';}
				else {tmplstr += '<div class="codeLibTemplateListFormatBody #{strClassBody}" style="width : #{numWidth}px; height : #{numHeight}px;">#{strBody}</div>';}
				if (obj.flagFooderUse) {
					tmplstr += '<div class="codeLibTemplateListFormatFooder" style="width : #{numWidth}px; height : ' + fooder + 'px; overflow : hidden;">';
						tmplstr += '<div class="codeLibTextDoubleLineIdle"></div>';
						tmplstr += '<div class="codeLibTextLine"></div>';
						tmplstr += '<div class="codeLibTemplateListFormatFooderWrap clearfix">';
							if (obj.flagFooderLeftUse) {
								tmplstr += '<div class="codeLibBaseFloatLeft">';
									tmplstr += '<ul>';
										if (!obj.strClassFooderLeft) {tmplstr += '<li class="codeLibTemplateListFormatFooderStr codeLibBaseMarginLeftFive">#{strTitleFooderLeft}</li>';}
										else {tmplstr += '<li class="codeLibTemplateListFormatFooderImg codeLibBaseMarginLeftFive #{strClassFooderLeft}">#{strTitleFooderLeft}</li>';}
									tmplstr += '</ul>';
								tmplstr += '</div>';
							}
							if (obj.flagFooderRightUse) {
								tmplstr += '<div class="codeLibBaseFloatRight">';
									tmplstr += '<ul>';
										if (!obj.strClassFooderRight) {tmplstr += '<li class="codeLibTemplateListFormatFooderStr codeLibBaseMarginRightFive">#{strTitleFooderRight}</li>';}
										else {tmplstr += '<li class="codeLibTemplateListFormatFooderImg codeLibBaseMarginRightFive #{strClassFooderRight}" >#{strTitleFooderRight}</li>';}
									tmplstr += '</ul>';
								tmplstr += '</div>';
							}
						tmplstr += '</div>';
					tmplstr += '</div>';
				}
			tmplstr += '</div>';
		}
		var data = tmplstr.interpolate(obj);

		return data;
	},

	/**
	 *
	*/
	updateStyle : function(obj)
	{
		if (obj.flagType == 'normalBox') {
			obj.numWrapWidth = obj.numWidth + 6;
			$(obj.id).style.width = obj.numWrapWidth + 'px';
			$(obj.id).down('.codeLibTemplateNormalBoxTopMiddle',0).style.width = obj.numWidth + 'px';
			$(obj.id).down('.codeLibTemplateNormalBoxMiddleMiddle',0).style.width = obj.numWidth + 'px';
			$(obj.id).down('.codeLibTemplateNormalBoxBottomMiddle',0).style.width = obj.numWidth + 'px';
			$(obj.id).down('.codeLibTemplateNormalBoxMiddleLeft',0).style.height = obj.numHeight + 'px';
			$(obj.id).down('.codeLibTemplateNormalBoxMiddleMiddle',0).style.height = obj.numHeight + 'px';
			$(obj.id).down('.codeLibTemplateNormalBoxMiddleRight',0).style.height = obj.numHeight + 'px';
		} else if (obj.flagType == 'shadowBox') {
			obj.numWrapWidth = obj.numWidth + 12;
			$(obj.id).style.width = obj.numWrapWidth + 'px';
			$(obj.id).down('.codeLibTemplateShadowBoxTopMiddle',0).style.width = obj.numWidth + 'px';
			$(obj.id).down('.codeLibTemplateShadowBoxMiddleMiddle',0).style.width = obj.numWidth + 'px';
			$(obj.id).down('.codeLibTemplateShadowBoxBottomMiddle',0).style.width = obj.numWidth + 'px';
			$(obj.id).down('.codeLibTemplateShadowBoxMiddleLeft',0).style.height = obj.numHeight + 'px';
			$(obj.id).down('.codeLibTemplateShadowBoxMiddleMiddle',0).style.height = obj.numHeight + 'px';
			$(obj.id).down('.codeLibTemplateShadowBoxMiddleRight',0).style.height = obj.numHeight + 'px';
		} else if (obj.flagType == 'menuBox') {
			obj.numWrapWidth = obj.numWidth + 12;
			$(obj.id).style.width = obj.numWrapWidth + 'px';
			$(obj.id).down('.codeLibTemplateMenuBoxTopMiddle',0).style.width = obj.numWidth + 'px';
			$(obj.id).down('.codeLibTemplateMenuBoxMiddleMiddle',0).style.width = obj.numWidth + 'px';
			$(obj.id).down('.codeLibTemplateMenuBoxBottomMiddle',0).style.width = obj.numWidth + 'px';
			$(obj.id).down('.codeLibTemplateMenuBoxMiddleLeft',0).style.height = obj.numHeight + 'px';
			$(obj.id).down('.codeLibTemplateMenuBoxMiddleMiddle',0).style.height = obj.numHeight + 'px';
			$(obj.id).down('.codeLibTemplateMenuBoxMiddleRight',0).style.height = obj.numHeight + 'px';
		} else if (obj.flagType == 'singleFormat') {
			var numMargin = 0;
			var header = 25;
			var fooder = 30;
			obj.numWidth = obj.numWidth - numMargin * 2;
			obj.numHeight = obj.numHeight - numMargin * 2  - header - fooder;
			if (!obj.flagHeaderUse) obj.numHeight = obj.numHeight + header;
			if (!obj.flagFooderUse) obj.numHeight = obj.numHeight + fooder;
			$(obj.id).down('.codeLibTemplateListFormatBody', 0).style.width = obj.numWidth + 'px';
			$(obj.id).down('.codeLibTemplateListFormatBody', 0).style.height = obj.numHeight + 'px';
		} else if (obj.flagType == 'normalFormat') {
			var numMargin = 10;
			var header = 27;
			var fooder = 32;
			obj.numWidth = obj.numWidth - numMargin * 2;
			obj.numHeight = obj.numHeight - numMargin * 2  - header - fooder;
			$(obj.id).down('.codeLibTemplateNormalFormatBody',0).style.width = obj.numWidth + 'px';
			$(obj.id).down('.codeLibTemplateNormalFormatBody',0).style.height = obj.numHeight + 'px';
			this.varStyleNormalFormatWidthTitle(obj);
			if (obj.flagHeaderLeftUse) {
				if (obj.flagHeaderRightWidth || obj.flagHeaderLeftWidth) {
					var ele = $(obj.id).down('.codeLibTemplateNormalFormatHeaderLeftWidthTitle',0);
					ele.style.width = obj.numWidthHeaderLeft + 'px';
				}
			}
			if (obj.flagHeaderRightUse) {
				if (obj.flagHeaderRightWidth || obj.flagHeaderLeftWidth) {
					var ele = $(obj.id).down('.codeLibTemplateNormalFormatHeaderRightWidthTitle',0);
					ele.style.width = obj.numWidthHeaderRight + 'px';
				}
			}
			if (obj.flagFooderUse) {
				$(obj.id).down('.codeLibTemplateNormalFormatFooderWrap',0).style.width = obj.numWidth + 'px';
			}
			if (obj.flagFooderLeftUse) {
				if (obj.flagFooderRightWidth || obj.flagFooderLeftWidth) {
					var ele = $(obj.id).down('.codeLibTemplateNormalFormatFooderLeftWidthTitle',0);
					ele.style.width = obj.numWidthFooderLeft + 'px';
				}
			}
			if (obj.flagFooderRightUse) {
				if (obj.flagFooderRightWidth || obj.flagFooderLeftWidth) {
					var ele = $(obj.id).down('.codeLibTemplateNormalFormatFooderRightWidthTitle',0);
					ele.style.width = obj.numWidthFooderRight + 'px';
				}
			}
		}
	},

	/**
	 *
	*/
	varStyleNormalFormatWidthTitle : function(obj)
	{
		var numMargin = 5;
		var numPadding = 20;
		var numWidthMin = 16;
		var numWidth = obj.numWidth;
		if (!obj.flagHeaderLeftUse) {
			obj.numWidthHeaderLeft = numMargin * 3;
		} else if (!obj.flagHeaderRightUse) {
			obj.numWidthHeaderRight = numMargin * 3;
		} else {
			if (!obj.flagHeaderRightWidth && obj.flagHeaderLeftWidth) {
				obj.numWidthHeaderLeft = numWidth - numPadding * 2 - numMargin * 3;
				obj.numWidthHeaderRight = numWidthMin;
			} else if (obj.flagHeaderRightWidth && !obj.flagHeaderLeftWidth) {
				obj.numWidthHeaderLeft = numWidthMin;
				obj.numWidthHeaderRight = numWidth - numPadding * 2 - numMargin * 3;
			}
		}

		if (!obj.flagFooderLeftUse) {
			obj.numWidthFooderLeft = numMargin * 3;
		} else if (!obj.flagFooderRightUse) {
			obj.numWidthFooderRight = numMargin * 3;
		} else {
			if (!obj.flagFooderRightWidth && obj.flagFooderLeftWidth) {
				obj.numWidthFooderLeft = numWidth - numPadding * 2 - numMargin * 3;
				obj.numWidthFooderRight = 0;
			} else if (obj.flagFooderRightWidth && !obj.flagFooderLeftWidth) {
				obj.numWidthFooderLeft = 0;
				obj.numWidthFooderRight = numWidth - numPadding * 2 - numMargin * 3;
			}
		}
	}
});


<?php }
}
?>