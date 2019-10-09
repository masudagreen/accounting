<?php /* Smarty version 3.1.24, created on 2016-08-18 12:50:21
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/btnPage.js" */ ?>
<?php
/*%%SmartyHeaderCode:138721399957b5af0d6ca159_86814949%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '6881e9ce4201dc12c44b1fec38b0194a40716bd5' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/btnPage.js',
      1 => 1471523676,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '138721399957b5af0d6ca159_86814949',
  'variables' => 
  array (
    'varsLoad' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_57b5af0d71cc48_46004457',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_57b5af0d71cc48_46004457')) {
function content_57b5af0d71cc48_46004457 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '138721399957b5af0d6ca159_86814949';
?>

/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_BtnPage = Class.create(Code_Lib_ExtLib,
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
		this._iniPage();
	},

	/**
	 *
	*/
	iniReload : function()
	{
		this.stopListener();
		this.removeWrap();
		this._iniPage();
	},

	/**
	 *
	*/
	_iniListener : function()
	{
		this._extListener();
	},

	/**
	 *
	*/
	_iniVars : function(obj)
	{
		this._extVars(obj);
	},

	/**
	 * page
	*/
	_staticPage : {numLeftIdle : 4, numRightIdle : 6, numLots : 10, numPadding : 5},
	_varsPage : null,
	_iniPage : function()
	{
		this._setPageWrap();
		this._varPage();
		this._setPageStatus();
		if (!this.vars.varsStatus.numRows) return;
		this._setPageTop();
		this._setPagePrev();
		this._setPage({arr : this._varsPage.arrayLot});
		this._setPageNext();
		this._setPageEnd();
	},

	setLock : function()
	{
		if (this.vars.varsStatus.flagLockUse) {
			this.vars.varsStatus.flagLockNow = 1;
			this.iniReload();
		}
	},

	cancelLock : function()
	{
		if (this.vars.varsStatus.flagLockUse) {
			this.vars.varsStatus.flagLockNow = 0;
			this.iniReload();
		}
	},

	/**
	 *
	*/
	eleWrapPage : {},
	_setPageWrap : function()
	{
		var ele = $(document.createElement('div'));
		ele.addClassName('codeLibBtnPageWrap');
		this.eleWrapPage.left = ele;
		this.eleInsertBtnLeft.innerHTML = '';
		this.eleInsertBtnLeft.insert(ele);
		ele = $(document.createElement('div'));
		ele.addClassName('codeLibBtnPageWrap');
		this.eleWrapPage.right = ele;
		this.eleInsertBtnRight.innerHTML = '';
		this.eleInsertBtnRight.insert(ele);
	},

	/**
	 *
	*/
	_varPageLot : function()
	{
		var cut = this._varsPage;
		if (cut.numRows > 0) {
			cut.lotAll = Math.floor(cut.numRows / cut.items);
			cut.itemIdle = cut.numRows - cut.lotAll * cut.items;
			if (cut.itemIdle > 0) {
				cut.lotAll++;
				cut.lotIdle = cut.lotAll;
			}
		}
	},

	/**
	 *
	*/
	_varPageTop : function()
	{
		if (!this.vars.varsStatus.flagTopUse) return;
		var cut = this._varsPage;
		if (cut.numRows > 0) {
			if (cut.numLotNow > 0) {
				cut.flagTop = 1;
				cut.lotTop = 0;
			}
		}
	},

	/**
	 *
	*/
	_varPagePrev : function()
	{
		if (!this.vars.varsStatus.flagPrevUse) return;
		var cut = this._varsPage;
		if (cut.numRows > 0) {
			if (cut.numLotNow > 0) {
				cut.flagPrev = 1;
				cut.lotPrev = cut.numLotNow - 1;
			}
		}
	},

	/**
	 *
	*/
	_varPageNext : function()
	{
		if (!this.vars.varsStatus.flagNextUse) return;
		var cut = this._varsPage;
		if (cut.numRows > 0) {
			if ((cut.lotAll-1) > cut.numLotNow) {
				cut.flagNext = 1;
				cut.lotNext = cut.numLotNow + 1;
			}
		}
	},

	/**
	 *
	*/
	_varPageEnd : function()
	{
		if (!this.vars.varsStatus.flagEndUse) return;
		var cut = this._varsPage;
		if (cut.numRows > 0) {
			if ((cut.lotAll-1) != cut.numLotNow) {
				cut.flagEnd = 1;
				cut.lotEnd = cut.lotAll-1;
			}
		}
	},

	/**
	 *
	*/
	_varPageLotNow : function()
	{
		var cut = this._varsPage;
		if (cut.numRows > 0) {
			if (cut.lotAll <= this._staticPage.numLots) {
				for (var i=0; i< cut.lotAll; i++) {
					cut.arrayLot.push(i);
				}
			} else {
				var rest = cut.allLot -1 - cut.numLotNow;
				if (cut.numLotNow < this._staticPage.numLeftIdle) {
					for (var i= 0; i<= cut.numLotNow; i++) {
						cut.arrayLot.push(i);
					}
					for (var i = cut.numLotNow + 1; i < this._staticPage.numLots; i++) {
						cut.arrayLot.push(i);
					}
				} else {
					for (var i = (cut.numLotNow); i >= (cut.numLotNow - this._staticPage.numLeftIdle); i--) {
						if (i >= cut.lotAll) break;
						cut.arrayLot.unshift(i);
					}
					for (var i = cut.numLotNow + 1; i < (cut.numLotNow + this._staticPage.numRightIdle); i++) {
						if (i >= cut.lotAll) break;
						cut.arrayLot.push(i);
					}
				}
			}
		}
	},

	/**
	 *
	*/
	_varPage : function()
	{
		this._varsPage = {
			numRows   : this.vars.varsStatus.numRows,
			itemIdle  : 0,
			numLotNow : this.vars.varsStatus.numLotNow,
			items     : parseFloat(this.insRoot.vars.varsSystem.status.numList),
			lotAll    : 0,
			lotIdle   : 0,
			arrayLot  : [],
			flagTop   : 0,
			lotTop    : 0,
			flagEnd   : 0,
			lotEnd    : 0,
			flagPrev  : 0,
			lotPrev   : 0,
			flagNext  : 0,
			lotNext   : 0,
		};
		this._varPageLot();
		this._varPageLotNow();
		this._varPageTop();
		this._varPagePrev();
		this._varPageNext();
		this._varPageEnd();
	},

	/**
	 *
	*/
	_setPageStatus : function()
	{
		if (!this.vars.varsStatus.flagStatusUse) return;
		var cut = this._varsPage;
		var displayStart = cut.numLotNow * cut.items + 1;
		var displayEnd = cut.numLotNow * cut.items;
		if (cut.itemIdle > 0 && !cut.flagNext) {
			displayEnd += cut.itemIdle;
		} else {
			displayEnd += cut.items;
		}
		var str = '';
		if (cut.numRows == 0) str = this.varsLoad.varsWhole.str.all + 0 + this.varsLoad.varsWhole.str.item;
		else str = displayStart + ' - ' + displayEnd + this.varsLoad.varsWhole.str.item1
			+ '('+ this.varsLoad.varsWhole.str.all + cut.numRows + this.varsLoad.varsWhole.str.item + ')';
		var ele = $(document.createElement('span'));
		ele.insert(str);
		if (this.vars.varsStatus.flagLockUse && this.vars.varsStatus.flagLockNow) {
			ele.addClassName('codeLibBaseFontCcc');
			ele.addClassName('codeLibBaseCursorDefault');
		}
		ele.style.paddingTop = this._staticPage.numPadding + 'px';
		ele.style.paddingLeft = this._staticPage.numPadding + 'px';
		this.eleWrapPage.right.insert(ele);
	},

	/**
	 *
	*/
	_setPageTop : function()
	{
		if (!this.vars.varsStatus.flagTopUse) return;
		var cut = this._varsPage;
		if (cut.flagTop) {
			if (this.vars.varsStatus.flagLockUse && this.vars.varsStatus.flagLockNow) {
				var ele = $(document.createElement('span'));
				ele.id = this.idSelf + 'BtnTop';
				ele.addClassName('codeLibBaseFontCcc');
				ele.addClassName('codeLibBaseCursorDefault');
				ele.insert(this.varsLoad.varsWhole.str.btnTop);
				this.eleWrapPage.left.insert(ele);
			} else {
				var insBtn = new Code_Lib_Btn();
				insBtn.iniBtnText({
					eleInsert  : this.eleWrapPage.left,
					id         : this.idSelf + 'BtnTop',
					strFunc    : 'checkPage',
					strTitle   : this.varsLoad.varsWhole.str.btnTop,
					insCurrent : this.insSelf,
					vars       : cut.lotTop
				});
				this._setListener({ins : insBtn});
			}
			$(this.idSelf + 'BtnTop').style.paddingTop = this._staticPage.numPadding + 'px';
			$(this.idSelf + 'BtnTop').style.paddingLeft = this._staticPage.numPadding + 'px';
		}
	},

	/**
	 *
	*/
	_setPagePrev : function()
	{
		if (!this.vars.varsStatus.flagPrevUse) return;
		var cut = this._varsPage;
		if (cut.flagPrev) {
			if (this.vars.varsStatus.flagLockUse && this.vars.varsStatus.flagLockNow) {
				var ele = $(document.createElement('span'));
				ele.id = this.idSelf + 'BtnPrev';
				ele.addClassName('codeLibBaseFontCcc');
				ele.addClassName('codeLibBaseCursorDefault');
				ele.insert(this.varsLoad.varsWhole.str.btnPrev);
				this.eleWrapPage.left.insert(ele);
			} else {
				var insBtn = new Code_Lib_Btn();
				insBtn.iniBtnText({
					eleInsert  : this.eleWrapPage.left,
					id         : this.idSelf + 'BtnPrev',
					strFunc    : 'checkPage',
					strTitle   : this.varsLoad.varsWhole.str.btnPrev,
					insCurrent : this.insSelf,
					vars       : cut.lotPrev
				});
				this._setListener({ins : insBtn});
			}
			$(this.idSelf + 'BtnPrev').style.paddingTop = this._staticPage.numPadding + 'px';
			$(this.idSelf + 'BtnPrev').style.paddingLeft = this._staticPage.numPadding + 'px';
		}
	},

	/**
	 *
	*/
	_setPageNext : function()
	{
		if (!this.vars.varsStatus.flagNextUse) return;
		var cut = this._varsPage;
		if (cut.flagNext) {
			if (this.vars.varsStatus.flagLockUse && this.vars.varsStatus.flagLockNow) {
				var ele = $(document.createElement('span'));
				ele.id = this.idSelf + 'BtnNext';
				ele.addClassName('codeLibBaseFontCcc');
				ele.addClassName('codeLibBaseCursorDefault');
				ele.insert(this.varsLoad.varsWhole.str.btnNext);
				this.eleWrapPage.left.insert(ele);
			} else {
				var insBtn = new Code_Lib_Btn();
				insBtn.iniBtnText({
					eleInsert  : this.eleWrapPage.left,
					id         : this.idSelf + 'BtnNext',
					strFunc    : 'checkPage',
					strTitle   : this.varsLoad.varsWhole.str.btnNext,
					insCurrent : this.insSelf,
					vars       : cut.lotNext
				});
				this._setListener({ins : insBtn});
			}
			$(this.idSelf + 'BtnNext').style.paddingTop = this._staticPage.numPadding + 'px';
			$(this.idSelf + 'BtnNext').style.paddingLeft = this._staticPage.numPadding + 'px';
		}
	},

	/**
	 *
	*/
	_setPageEnd : function()
	{
		if (!this.vars.varsStatus.flagEndUse) return;
		var cut = this._varsPage;
		if (cut.flagEnd) {
			if (this.vars.varsStatus.flagLockUse && this.vars.varsStatus.flagLockNow) {
				var ele = $(document.createElement('span'));
				ele.id = this.idSelf + 'BtnEnd';
				ele.addClassName('codeLibBaseFontCcc');
				ele.addClassName('codeLibBaseCursorDefault');
				ele.insert(this.varsLoad.varsWhole.str.btnEnd);
				this.eleWrapPage.left.insert(ele);
			} else {
				var insBtn = new Code_Lib_Btn();
				insBtn.iniBtnText({
					eleInsert  : this.eleWrapPage.left,
					id         : this.idSelf + 'BtnEnd',
					strFunc    : 'checkPage',
					strTitle   : this.varsLoad.varsWhole.str.btnEnd,
					insCurrent : this.insSelf,
					vars       : cut.lotEnd
				});
				this._setListener({ins : insBtn});
			}
			$(this.idSelf + 'BtnEnd').style.paddingTop = this._staticPage.numPadding + 'px';
			$(this.idSelf + 'BtnEnd').style.paddingLeft = this._staticPage.numPadding + 'px';
		}
	},

	/**
	 *
	*/
	_setPage : function(obj)
	{
		var cut = this._varsPage;
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i] == cut.numLotNow) {
				var ele = $(document.createElement('span'));
				ele.id = this.idSelf + 'Btn' + obj.arr[i];
				ele.unselectable = 'on';
				ele.addClassName('unselect');
				ele.addClassName('codeLibBaseCursorDefault');
				ele.addClassName('codeLibBtnPageNow');
				ele.insert(obj.arr[i]+1);
				ele.style.paddingTop = this._staticPage.numPadding + 'px';
				ele.style.paddingLeft = this._staticPage.numPadding + 'px';
				this.eleWrapPage.left.insert(ele);
				continue;
			}
			if (this.vars.varsStatus.flagLockUse && this.vars.varsStatus.flagLockNow) {
				var ele = $(document.createElement('span'));
				ele.id = this.idSelf + 'Btn' + obj.arr[i];
				ele.addClassName('codeLibBaseFontCcc');
				ele.addClassName('codeLibBaseCursorDefault');
				ele.insert(obj.arr[i] + 1);
				this.eleWrapPage.left.insert(ele);
			} else {
				var insBtn = new Code_Lib_Btn();
				insBtn.iniBtnText({
					eleInsert  : this.eleWrapPage.left,
					id         : this.idSelf + 'Btn' + obj.arr[i],
					strFunc    : 'checkPage',
					strTitle   : obj.arr[i] + 1,
					insCurrent : this.insSelf,
					vars       : obj.arr[i]
				});
				this._setListener({ins : insBtn});
			}
			$(this.idSelf + 'Btn' + obj.arr[i]).style.paddingTop = this._staticPage.numPadding + 'px';
			$(this.idSelf + 'Btn' + obj.arr[i]).style.paddingLeft = this._staticPage.numPadding + 'px';
		}
	},

	/**
	 *
	*/
	checkPage : function(obj)
	{
		this.setLock();
		this.allot({
			insCurrent : this.insCurrent,
			from       : 'eventPage',
			vars   : {
				numLotPast : this.vars.varsStatus.numLotNow,
				numLotNow  : obj.vars.vars
			}
		});
	}

});

<?php }
}
?>