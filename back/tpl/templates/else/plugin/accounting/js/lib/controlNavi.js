{literal}
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Plugin_Accounting_Lib_ControlNavi = Class.create(Code_Lib_ControlNavi,
{

	/**
	 *
	*/
	_setSearch : function()
	{
		this.insSearch = new Code_Plugin_Accounting_Lib_Search({
			eleInsertBtnLeft  : this.insUnder.eleFormat.fooder.down('.codeLibBaseMarginLeftFive', 0),
			eleInsertBtnRight : this.insUnder.eleFormat.fooder.down('.codeLibBaseMarginRightFive', 0),
			eleInsert         : this.insUnder.eleFormat.body,
			insRoot           : this.insRoot,
			insCurrent        : this.insSelf,
			idSelf            : this.idSelf + 'Search',
			allot             : this._getSearchAllot(),
			vars              : this.vars.search.varsDetail
		});
	}
});
{/literal}
