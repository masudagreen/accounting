<?php /* Smarty version 3.1.24, created on 2016-08-18 12:50:23
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/timeDisplay.js" */ ?>
<?php
/*%%SmartyHeaderCode:210263962457b5af0f172834_63730092%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '4601586aae1fb7199efecdb7337c3c8c7cdb9e06' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/timeDisplay.js',
      1 => 1471523676,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '210263962457b5af0f172834_63730092',
  'variables' => 
  array (
    'varsLoad' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_57b5af0f1958b3_11310503',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_57b5af0f1958b3_11310503')) {
function content_57b5af0f1958b3_11310503 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '210263962457b5af0f172834_63730092';
?>

/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_TimeDisplay = Class.create({

	varsLoad : <?php echo $_smarty_tpl->tpl_vars['varsLoad']->value;?>
,


	/**
	 * obj = {
	 * 	flagType : string,
	 * 	vars : object,
	 * }
	*/
	vars : null, posVars : null,
	get : function(obj)
	{
		this.vars = (Object.toJSON(obj.vars)).evalJSON();
		this.vars.numMonth++;
		this.posVars = (Object.toJSON(this.vars)).evalJSON();
		var str = '';
		if (this.vars.numMonth < 10) this.vars.numMonth = '0' + this.vars.numMonth;
		if (this.vars.numDate < 10) this.vars.numDate = '0' + this.vars.numDate;
		if (this.vars.numHour < 10) this.vars.numHour = '0' + this.vars.numHour;
		if (this.vars.numMin < 10) this.vars.numMin = '0' + this.vars.numMin;
		if (this.vars.numSec < 10) this.vars.numSec = '0' + this.vars.numSec;

		if (obj.flagType == 1) {
			str = this.vars.numYear
				+ this.varsLoad.strYear
				+ this.vars.numMonth
				+ this.varsLoad.strMonth
				+ this.vars.numDate
				+ this.varsLoad.strDate
				+ '('+this.varsLoad.arrayWeek[this.vars.numDay]+') '
				+ this.vars.numHour
				+ this.varsLoad.strHour
				+ this.vars.numMin
				+ this.varsLoad.strMin;

		} else if (obj.flagType == 2) {
			str = this.vars.numYear
				+ this.varsLoad.strYear
				+ this.vars.numMonth
				+ this.varsLoad.strMonth
				+ this.vars.numDate
				+ this.varsLoad.strDate + '('+this.varsLoad.arrayWeek[this.vars.numDay]+')';

		} else if (obj.flagType == 3) {
			str = this.vars.numYear
				+ '/' + this.vars.numMonth
				+ '/' + this.vars.numDate
				+ '  ' + this.vars.numHour
				+ ':' + this.vars.numMin;

		} else if (obj.flagType == 4) {
			str = this.vars.numYear
				+ '/' + this.vars.numMonth
				+ '/' + this.vars.numDate;

		} else if (obj.flagType == 5) {
			str = this.vars.numHour
				+ this.varsLoad.strHour
				+ this.vars.numMin
				+ this.varsLoad.strMin + '(' +this.vars.numSec + this.varsLoad.strSec + ')';

		} else if (obj.flagType == 6) {
			str = this.vars.numYear
				+ this.varsLoad.strYear
				+ this.vars.numMonth
				+ this.varsLoad.strMonth
				+ this.vars.numDate
				+ this.varsLoad.strDate
				+ '('+this.varsLoad.arrayWeek[this.vars.numDay]+') '
				+ this.vars.numHour
				+ this.varsLoad.strHour
				+ this.vars.numMin
				+ this.varsLoad.strMin
				+ this.vars.numSec
				+ this.varsLoad.strSec;

		} else if (obj.flagType == 7) {
			str = this.posVars.numYear
				+ '/' + this.posVars.numMonth
				+ '/' + this.posVars.numDate;

		} else if (obj.flagType == 8) {
			str = this.vars.numYear
				+ this.varsLoad.strYear
				+ this.vars.numMonth
				+ this.varsLoad.strMonth
				+ this.vars.numDate
				+ this.varsLoad.strDate;

		} else if (obj.flagType == 9) {
			str = this.vars.numYear
			+ '/' + this.vars.numMonth
			+ '/' + this.vars.numDate
			+ '-' + this.vars.numHour
			+ ':' + this.vars.numMin;

		}

		return str;
	},

	/*
		{
			stamp : num,
			numYear : num
		}
	 * */
	getNengoYear : function(obj)
	{
		var numYear = parseFloat(obj.numYear);
		var flag = this.getFlagNengo({stamp : obj.stamp});
		if (flag == 'Meiji') {
			numYear -= 1867;

		} else if (flag == 'Taishou') {
			numYear -= 1911;

		} else if (flag == 'Shouwa') {
			numYear -= 1925;

		} else if (flag == 'Heisei') {
			numYear -= 1988;
		}

		return numYear;
	},

	/*
		{
			stamp : num,
			numYear : num
		}
	 * */
	getStrNengo : function(obj)
	{
		var flag = this.getFlagNengo({stamp : obj.stamp});
		if (!flag) return '';
		var strNengo = this.varsLoad['str' + flag];
		var strNengoYear = this.getNengoYear({
			stamp   : obj.stamp,
			numYear : obj.numYear
		});

		return strNengo + strNengoYear;
	},

	/*
		meiji 1868/09/08 -3197178000
		taishou 1912/07/30 -1812186000
		shouwa 1926/12/25 -1357635600
		heisei 1989/01/08   600188400
		{
			stamp : num
		}
	 * */
	getFlagNengo : function(obj)
	{
		var stamp = parseFloat(obj.stamp);
		if (-3197178000 <= stamp && stamp < -1812186000) {
			return 'Meiji';

		} else if (-1812186000 <= stamp && stamp < -1357635600) {
			return 'Taishou';

		} else if (-1357635600 <= stamp && stamp < 600188400) {
			return 'Shouwa';

		} else if (600188400 <= stamp) {
			return 'Heisei';
		}

		return '';
	}

});
<?php }
}
?>