{literal}
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_LockTemp = Class.create({

	/**
	 *
	*/
	insListener : null,
	_iniListener : function()
	{
		this.insListener = new Code_Lib_Listener();
	},

	/**
	 * obj = {
	 * 	idSelf      : string,
	 * 	numZIndex   : int,
	 * 	idInsert    : string,
	 * 	eleInsert   : ele,
	 * 	insCurrent  : instance,
	 * 	strFunc     : string,
	 * 	flagHideUse : int,
	 * }
	*/
	vars : null, eleLock : null,
	iniLoad : function(obj)
	{
		this._iniListener();
		this.vars = obj;
		this._setLock();
		this._setLockListener();
	},

	/**
	 *
	*/
	_setLock : function(obj)
	{
		var eleLock = $(document.createElement('div'));
		eleLock.id = this.vars.idSelf;
		eleLock.addClassName('codeLibLockView');
		if (this.vars.eleInsert) this.vars.eleInsert.insert(eleLock);
		else $(this.vars.idInsert).insert(eleLock);

		this.eleLock = eleLock;
		this._styleLock();
	},

	/**
	 *
	*/
	_setLockListener : function()
	{
		this.insListener.set({ bindAsEvent : 1, insCurrent : this, event : 'mousedown',
			strFunc : '_mousedownLock', ele : this.eleLock, vars : ''
		});
	},

	/**
	 *
	*/
	_styleLock : function()
	{
		this.eleLock.setStyle({
			zIndex : this.vars.numZIndex
		});
	},

	/**
	 *
	*/
	hideLock : function()
	{
		if(this.eleLock) {
			this.eleLock.hide();
		}
	},

	/**
	 *
	*/
	showLock : function()
	{
		if(this.eleLock) {
			this.eleLock.show();
			this._styleLock();
		}
	},

	/**
	 *
	*/
	_mousedownLock : function(evt, obj) {
		if(obj) evt.stop();
		else obj = evt;
		if(this.eleLock) {
			if(this.vars.flagHideUse) {
				this.eleLock.hide();

			} else {
				this.eleLock.remove();
				this.eleLock = null;
			}
		}
		this.vars.insCurrent[this.vars.strFunc]();
	}
});
{/literal}
