<?php

$vars = array(
	array(
	    'table' => 'basePreference',
	    'db'    => 'type=InnoDB',
	    'index' => array(
				array( 'column' => 'id', 'type' => 'bigint unsigned auto_increment,primary key(id)',),

				//stamp
				array( 'column' => 'stampRegister',      'type' => 'bigint not null',),
				array( 'column' => 'stampUpdate',	'type' => 'bigint not null',),

				array( 'column' => 'jsonStampUpdate',    'type' => 'mediumtext',),

				//system
				array( 'column' => 'flagMaintenance',  'type' => 'int(1) unsigned default 0',),
				array( 'column' => 'arrCommaIdAccountMaintenance',   'type' => 'mediumtext',),
				array( 'column' => 'numTimeZone',      'type' => 'int',),
				array( 'column' => 'strTopUrl',	'type' => 'text',),
				array( 'column' => 'numAutoLock',      'type' => 'int unsigned default 3',),

				array( 'column' => 'numPasswordLimit', 'type' => 'varchar(7) default 0',),
				array( 'column' => 'numPassword',      'type' => 'int default 4',),

				array( 'column' => 'arrCommaLockAccount', 'type' => 'mediumtext',),

				array( 'column' => 'flagLoginMail', 'type' => 'int(1) unsigned default 0',),
				array( 'column' => 'flagAccessUnknownMail', 'type' => 'int(1) unsigned default 0',),
				array( 'column' => 'flagLoginSecond', 'type' => 'int(1) unsigned default 0',),

				array( 'column' => 'flagVersionUpdate',   'type' => 'int(1) unsigned default 0',),

				//webmaster
				array( 'column' => 'strSiteName',    'type' => 'text not null',),
				array( 'column' => 'strSiteUrl',     'type' => 'text',),
				array( 'column' => 'strSiteMailPc', 'type' => 'text not null',),

				//desktop
				array( 'column' => 'numAutoMustLogout',   'type' => 'int unsigned default 0',),

				//form
				array( 'column' => 'flagForgot', 'type' => 'int(1) unsigned default 0',),
				array( 'column' => 'flagSign',   'type' => 'int(1) unsigned default 0',),

				//format   '["123.123.123.123"]'
				array( 'column' => 'jsonIpAccessAccept', 'type' => 'mediumtext',),

				//format   '["123.123.123.123/23","123.123.123.123-123.123.123.125"]'
				array( 'column' => 'jsonIpSubnetAccessAccept', 'type' => 'mediumtext',),

				//reject
				array( 'column' => 'flagReject',   'type' => 'int(1) unsigned default 1',),
								
				//format   '["123.123.123.123"]'
				array( 'column' => 'jsonIpAccessReject', 'type' => 'mediumtext',),

				//format   '["123.123.123.123/23","123.123.123.123-123.123.123.125"]'
				array( 'column' => 'jsonIpSubnetAccessReject', 'type' => 'mediumtext',),

				//format   '["123.123.123.123"]'
				array( 'column' => 'jsonIpSignReject', 'type' => 'mediumtext',),

				//format   '["123.123.123.123/23"]'
				array( 'column' => 'jsonIpSubnetSignReject', 'type' => 'mediumtext',),

				//format   '["a@a.com"]'
				array( 'column' => 'jsonMailSignReject', 'type' => 'mediumtext',),

				//format   '["a.com"]'
				//insert comma into first and end.
				array( 'column' => 'jsonMailHostSignReject', 'type' => 'mediumtext',),

				//format   '["accouting"]' installed
				array( 'column' => 'jsonModule', 'type' => 'mediumtext',),

				array( 'column' => 'strVersion', 'type' => 'varchar(11)',),

				/*
					{
						'1.0.0' : 1,
					}
				*/
				array( 'column' => 'jsonVersion', 'type' => 'mediumtext',),
	    ),
	),
	array(
	    'table' => 'baseAccount',
	    'db'    => 'type=InnoDB',
	    'index' => array(
				array( 'column' => 'id', 'type' => 'bigint unsigned auto_increment,primary key(id)',),

				//stamp
				array( 'column' => 'stampRegister', 'type' => 'bigint not null',),
				array( 'column' => 'stampUpdate',   'type' => 'bigint not null',),

				//lock
				array( 'column' => 'flagLock', 'type' => 'int(1) unsigned default 0',),

				//account
				array( 'column' => 'flagWebmaster', 'type' => 'int(1) unsigned default 0',),
				array( 'column' => 'strCodeName',   'type' => 'varchar(100) not null',),
				array( 'column' => 'idLogin',       'type' => 'text not null',),
				array( 'column' => 'strPassword',   'type' => 'text not null',),
				array( 'column' => 'stampUpdatePassword',   'type' => 'bigint',),
				array( 'column' => 'strMailPc',     'type' => 'text not null',),

	    		array( 'column' => 'flagLoginMail', 'type' => 'int(1) unsigned default 0',),
	    		array( 'column' => 'flagLoginSecond', 'type' => 'int(1) unsigned default 0',),

				//mobile
				array( 'column' => 'strMailMobile',    'type' => 'text',),
				array( 'column' => 'idMobile',	 'type' => 'text',),
				array( 'column' => 'strMobileCarrier', 'type' => 'varchar(100)',),

				//desktop
				array( 'column' => 'numTimeZone',   'type' => 'int',),
				array( 'column' => 'strLang',       'type' => 'varchar(2)',),
				array( 'column' => 'strHoliday',    'type' => 'varchar(2)',),
				array( 'column' => 'numList',       'type' => 'int unsigned default 25',),
				array( 'column' => 'numAutoLogout', 'type' => 'int unsigned default 0',),
				array( 'column' => 'numAutoPopup',  'type' => 'int unsigned default 0',),
				array( 'column' => 'strAutoBoot',   'type' => 'varchar(100) default "base"',),

				//term
				array( 'column' => 'idTerm', 'type' => 'bigint unsigned',),

				//Module
				array( 'column' => 'idModule', 'type' => 'bigint unsigned',),

				array( 'column' => 'arrSpaceStrTag', 'type' => 'mediumtext',),

				//news check stamp
				array( 'column' => 'jsonStampCheck',    'type' => 'mediumtext',),

				array( 'column' => 'flagDefault', 'type' => 'int default 0',),
	    )
	),
	array(
	    'table' => 'baseAccountId',
	    'db'    => 'type=InnoDB',
	    'index' => array(
				array( 'column' => 'id', 'type' => 'bigint unsigned',),
				array( 'column' => 'strCodeName', 'type' => 'varchar(100) not null',),
	    )
	),
	array(
	    'table' => 'baseAccountMemo',
	    'db'    => 'type=InnoDB',
	    'index' => array(
				array( 'column' => 'id', 'type' => 'bigint unsigned auto_increment,primary key(id)',),

				//stamp
				array( 'column' => 'stampRegister', 'type' => 'bigint not null',),
				array( 'column' => 'stampUpdate',   'type' => 'bigint not null',),

				array( 'column' => 'idAccount', 'type' => 'bigint unsigned not null',),

				array( 'column' => 'flagColumn',      'type' => 'varchar(50) not null',),
				array( 'column' => 'jsonData', 'type' => 'mediumtext',),
	    )
	),
	array(
	    'table' => 'baseTerm',
	    'db'    => 'type=InnoDB',
	    'index' => array(
				array( 'column' => 'id', 'type' => 'bigint unsigned auto_increment,primary key(id)',),

				//stamp
				array( 'column' => 'stampRegister', 'type' => 'bigint not null',),
				array( 'column' => 'stampUpdate',   'type' => 'bigint not null',),

				array( 'column' => 'strTitle', 'type' => 'text not null',),

				//term
				array( 'column' => 'stampStart', 'type' => 'bigint not null',),
				array( 'column' => 'stampEnd',  'type' => 'bigint',),

				array( 'column' => 'arrSpaceStrTag', 'type' => 'mediumtext',),

				array( 'column' => 'flagDefault', 'type' => 'int default 0',),
	    )
	),
	array(
	    'table' => 'baseModule',
	    'db'    => 'type=InnoDB',
	    'index' => array(
				array( 'column' => 'id', 'type' => 'bigint unsigned auto_increment,primary key(id)',),

				//stamp
				array( 'column' => 'stampRegister', 'type' => 'bigint not null',),
				array( 'column' => 'stampUpdate',   'type' => 'bigint not null',),

				array( 'column' => 'strTitle', 'type' => 'text not null',),

				array( 'column' => 'arrCommaIdModuleUser', 'type' => 'mediumtext',),
				array( 'column' => 'arrCommaIdModuleAdmin', 'type' => 'mediumtext',),

				array( 'column' => 'arrSpaceStrTag', 'type' => 'mediumtext',),

				array( 'column' => 'flagDefault', 'type' => 'int default 0',),
	    )
	),
	array(
	    'table' => 'baseAccessLog',
	    'db'    => 'type=InnoDB',
	    'index' => array(
				array( 'column' => 'id','type' => 'bigint unsigned auto_increment,primary key(id)',),
				array( 'column' => 'stampRegister', 'type' => 'bigint not null',),
				array( 'column' => 'ip',     'type' => 'varchar(15) not null',),
				array( 'column' => 'strHost',     'type' => 'text',),
				array( 'column' => 'idAccount',     'type' => 'bigint unsigned',),
				array( 'column' => 'strDbType',     'type' => 'text',),
				array( 'column' => 'strDevice',     'type' => 'text',),
				array( 'column' => 'idModule',     'type' => 'text',),
				array( 'column' => 'strChild',     'type' => 'text',),
				array( 'column' => 'strExt',     'type' => 'text',),
				array( 'column' => 'strFunc',     'type' => 'text',),
				array( 'column' => 'jsonQuery',     'type' => 'mediumtext',),
	    ),
	),
	array(
	    'table' => 'baseAccessUnknown',
	    'db'    => 'type=InnoDB',
	    'index' => array(
				array( 'column' => 'id','type' => 'bigint unsigned auto_increment,primary key(id)',),
				array( 'column' => 'ip',     'type' => 'varchar(15) not null',),
	    ),
	),
	array(
	    'table' => 'basePublish',
	    'db'    => 'type=InnoDB',
	    'index' => array(
				array( 'column' => 'id', 'type' => 'bigint unsigned auto_increment,primary key(id)',),
				array( 'column' => 'stampRegister',   'type' => 'bigint not null',),
				array( 'column' => 'session',	 'type' => 'varchar(100)',),
				array( 'column' => 'idAccount',     'type' => 'bigint unsigned',),
	    )
	),
	array(
	    'table' => 'baseLock',
	    'db'    => 'type=InnoDB',
	    'index' => array(
				array( 'column' => 'id', 'type' => 'bigint unsigned auto_increment,primary key(id)',),
				array( 'column' => 'stampRegister',   'type' => 'bigint not null',),
				array( 'column' => 'idAccount',     'type' => 'bigint unsigned',),
	    )
	),
	array(
	    'table' => 'baseApplySign',
	    'db'    => 'type=InnoDB',
	    'index' => array(
				array( 'column' => 'id', 'type' => 'bigint unsigned auto_increment,primary key(id)',),
				array( 'column' => 'stampRegister',   'type' => 'bigint not null',),
				array( 'column' => 'session',	 'type' => 'varchar(100) not null',),
				array( 'column' => 'ip',	      'type' => 'varchar(15) not null',),
				array( 'column' => 'strCodeName',	 'type' => 'text',),
				array( 'column' => 'idLogin',	 'type' => 'text',),
				array( 'column' => 'strMailPc',       'type' => 'text',),
				array( 'column' => 'flagAttest',       'type' => 'int default 0',),
	    )
	),
	array(
	    'table' => 'baseApplyChange',
	    'db'    => 'type=InnoDB',
	    'index' => array(
				array( 'column' => 'id', 'type' => 'bigint unsigned auto_increment,primary key(id)',),
				array( 'column' => 'stampRegister',   'type' => 'bigint not null',),
				array( 'column' => 'idAccount', 'type' => 'bigint unsigned',),
				array( 'column' => 'session',	 'type' => 'varchar(100) not null',),
				array( 'column' => 'ip',	      'type' => 'varchar(15) not null',),
				array( 'column' => 'strCodeName',	 'type' => 'text',),
				array( 'column' => 'idLogin',	 'type' => 'text',),
				array( 'column' => 'strMailPc',       'type' => 'text',),
				array( 'column' => 'flagAttest',       'type' => 'int default 0',),
	    )
	),
	array(
	    'table' => 'baseApplyForgot',
	    'db'    => 'type=InnoDB',
	    'index' => array(
				array( 'column' => 'id', 'type' => 'bigint unsigned auto_increment,primary key(id)',),
				array( 'column' => 'stampRegister',   'type' => 'bigint not null',),
				array( 'column' => 'idAccount', 'type' => 'bigint unsigned not null',),
				array( 'column' => 'session',	 'type' => 'varchar(100) not null',),
				array( 'column' => 'ip',	      'type' => 'varchar(15) not null',),
	    )
	),
	array(
	    'table' => 'baseApiAccount',
	    'db'    => 'type=InnoDB',
	    'index' => array(
				array( 'column' => 'id', 'type' => 'bigint unsigned auto_increment,primary key(id)',),
				array( 'column' => 'stampRegister',   'type' => 'bigint not null',),
				array( 'column' => 'stampUpdate',   'type' => 'bigint not null',),
				array( 'column' => 'ip',	    'type' => 'varchar(15) not null',),
				array( 'column' => 'strSiteUrl',     'type' => 'text',),
				array( 'column' => 'idAccount',     'type' => 'bigint unsigned not null',),
				array( 'column' => 'arrSpaceStrTag', 'type' => 'mediumtext',),
	    ),
	),
	array(
	    'table' => 'baseSession',
	    'db'    => 'type=InnoDB',
	    'index' => array(
				array( 'column' => 'stampRegister', 'type' => 'bigint not null',),
				array( 'column' => 'ip',	    'type' => 'varchar(15) not null',),
				array( 'column' => 'idCookie',       'type' => 'varchar(100) not null',),
				array( 'column' => 'idMobile',      'type' => 'text',),
				array( 'column' => 'idAccount',     'type' => 'bigint unsigned not null',),
				array( 'column' => 'flagAPI',       'type' => 'int default 0',),
	    ),
	),
	array(
	    'table' => 'baseLoginSecond',
	    'db'    => 'type=InnoDB',
	    'index' => array(
				array( 'column' => 'stampRegister', 'type' => 'bigint not null',),
				array( 'column' => 'ip',	    'type' => 'varchar(15) not null',),
				array( 'column' => 'session',       'type' => 'varchar(100) not null',),
				array( 'column' => 'idAccount',     'type' => 'bigint unsigned not null',),
	    ),
	),
	array(
	    'table' => 'baseToken',
	    'db'    => 'type=InnoDB',
	    'index' => array(
				array( 'column' => 'stampRegister', 'type' => 'bigint not null',),
				array( 'column' => 'token',       'type' => 'varchar(100) not null',),
				array( 'column' => 'idAccount',     'type' => 'bigint unsigned',),
	    ),
	),
	array(
	    'table' => 'baseLoginPassword',
	    'db'    => 'type=InnoDB',
	    'index' => array(
				array( 'column' => 'stampRegister', 'type' => 'bigint not null',),
				array( 'column' => 'idAccount',     'type' => 'bigint unsigned',),
				array( 'column' => 'strPassword',   'type' => 'text not null',),
	    ),
	),
	array(
	    'table' => 'baseLoginIdLogin',
	    'db'    => 'type=InnoDB',
	    'index' => array(
				array( 'column' => 'stampRegister', 'type' => 'bigint not null',),
				array( 'column' => 'idLogin',   'type' => 'text not null',),
	    ),
	),
	array(
	    'table' => 'baseLoginMiss',
	    'db'    => 'type=InnoDB',
	    'index' => array(
			array( 'column' => 'stampRegister', 'type' => 'bigint not null',),
			array( 'column' => 'ip',	    'type' => 'varchar(15) not null',),
			array( 'column' => 'idLogin',       'type' => 'text not null',),
			array( 'column' => 'strPassword',   'type' => 'text not null',),
			array( 'column' => 'strError',   'type' => 'text not null',),
	    ),
	),
);

