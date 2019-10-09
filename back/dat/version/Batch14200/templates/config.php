<?php

$vars = array(
	array(
	    'table' => 'accountingPreference',
	    'db'    => 'type=InnoDB',
	    'index' => array(
				array( 'column' => 'id', 'type' => 'int unsigned auto_increment,primary key(id)',),

				//stamp
				array( 'column' => 'stampRegister',      'type' => 'bigint not null',),
				array( 'column' => 'stampUpdate',	'type' => 'bigint not null',),

				array( 'column' => 'jsonStampUpdate',    'type' => 'longtext',),

				array( 'column' => 'flagMaintenance',  'type' => 'int(1) unsigned default 0',),
				array( 'column' => 'arrCommaIdAccountMaintenance',   'type' => 'longtext',),

				array( 'column' => 'strVersion', 'type' => 'varchar(11)',),
				array( 'column' => 'flagIdAccountTitle', 'type' => 'int(1) unsigned default 0',),
				array( 'column' => 'accessCode', 'type' => 'varchar(100)',),

				/*
					{
						'1.0.0' : 1,
					}
				*/
				array( 'column' => 'jsonVersion', 'type' => 'longtext',),

				/*
					{
						'id' : array(),
					}
				*/
				array( 'column' => 'jsonIdAutoIncrement', 'type' => 'longtext',),
	    ),
	),
	array(
	    'table' => 'accountingAccount',
	    'db'    => 'type=InnoDB',
	    'index' => array(
				array( 'column' => 'id', 'type' => 'int unsigned auto_increment,primary key(id)',),
				array( 'column' => 'stampRegister', 'type' => 'bigint not null',),
				array( 'column' => 'stampUpdate',   'type' => 'bigint not null',),
				array( 'column' => 'idAccount', 'type' => 'int unsigned',),
				array( 'column' => 'flagAdmin', 'type' => 'int unsigned default 1',),
				array( 'column' => 'idEntityCurrent', 'type' => 'int unsigned default 1',),
				array( 'column' => 'numFiscalPeriodCurrent', 'type' => 'int unsigned default 1',),

				array( 'column' => 'arrCommaIdEntity', 'type' => 'longtext',),
	    )
	),

	array(
	    'table' => 'accountingAccountEntity',
	    'db'    => 'type=InnoDB',
	    'index' => array(
				array( 'column' => 'id', 'type' => 'int unsigned auto_increment,primary key(id)',),

				array( 'column' => 'idAccount', 'type' => 'int unsigned default 1',),
				array( 'column' => 'idEntity', 'type' => 'int unsigned default 1',),

				array( 'column' => 'idAuthority', 'type' => 'int unsigned default 1',),

				array( 'column' => 'idAccess', 'type' => 'int unsigned default 1',),

				array( 'column' => 'strMailFile', 'type' => 'text',),


	    )
	),
	array(
	    'table' => 'accountingAccountMemo',
	    'db'    => 'type=InnoDB',
	    'index' => array(
				array( 'column' => 'id', 'type' => 'int unsigned auto_increment,primary key(id)',),
				array( 'column' => 'stampRegister', 'type' => 'bigint not null',),
				array( 'column' => 'stampUpdate',   'type' => 'bigint not null',),
				array( 'column' => 'idAccount', 'type' => 'int unsigned',),
				array( 'column' => 'idEntity', 'type' => 'int unsigned default 0',),
				array( 'column' => 'flagColumn',      'type' => 'varchar(50) not null',),
				array( 'column' => 'jsonData', 'type' => 'longtext',),
	    )
	),
	array(
	    'table' => 'accountingAccountId',
	    'db'    => 'type=InnoDB',
	    'index' => array(
				array( 'column' => 'id', 'type' => 'int unsigned',),
				array( 'column' => 'strCodeName', 'type' => 'varchar(100) not null',),
	    )
	),

	array(
	    'table' => 'accountingEntityDepartment',
	    'db'    => 'type=InnoDB',
	    'index' => array(
				array( 'column' => 'id', 'type' => 'int unsigned auto_increment,primary key(id)',),
				array( 'column' => 'stampRegister', 'type' => 'bigint not null',),
				array( 'column' => 'stampUpdate',   'type' => 'bigint not null',),
				array( 'column' => 'idDepartment', 'type' => 'int unsigned default 0',),
				array( 'column' => 'idEntity', 'type' => 'int unsigned not null',),
				array( 'column' => 'numFiscalPeriod', 'type' => 'int unsigned default 1',),
				array( 'column' => 'strTitle',     'type' => 'varchar(100)',),
				array( 'column' => 'arrSpaceStrTag', 'type' => 'mediumtext',),
	    )
	),
	array(
	    'table' => 'accountingEntityDepartmentFSValueJpn',
	    'db'    => 'type=InnoDB',
	    'index' => array(
				array( 'column' => 'id', 'type' => 'int unsigned auto_increment,primary key(id)',),

				array( 'column' => 'stampRegister', 'type' => 'bigint not null',),
				array( 'column' => 'stampUpdate',   'type' => 'bigint not null',),

				array( 'column' => 'idDepartment', 'type' => 'int unsigned not null',),
				array( 'column' => 'idEntity', 'type' => 'int unsigned not null',),
				array( 'column' => 'numFiscalPeriod', 'type' => 'int unsigned default 1',),

				array( 'column' => 'jsonJgaapAccountTitlePL', 'type' => 'longtext',),
				array( 'column' => 'jsonJgaapAccountTitleBS', 'type' => 'longtext',),
				array( 'column' => 'jsonJgaapAccountTitleCR', 'type' => 'longtext',),

				array( 'column' => 'jsonJgaapFSPL', 'type' => 'longtext',),
				array( 'column' => 'jsonJgaapFSBS', 'type' => 'longtext',),
				array( 'column' => 'jsonJgaapFSCR', 'type' => 'longtext',),
	    )
	),
	array(
	    'table' => 'accountingAuthority',
	    'db' => 'type=InnoDB',
	    'index' => array(
				array( 'column' => 'id', 'type' => 'int unsigned auto_increment,primary key(id)',),

				//stamp
				array( 'column' => 'stampRegister', 'type' => 'bigint not null',),
				array( 'column' => 'stampUpdate',   'type' => 'bigint not null',),

				array( 'column' => 'strTitle',     'type' => 'varchar(100)',),

				array( 'column' => 'flagMySelect', 'type' => 'int unsigned default 1',),
				array( 'column' => 'flagMyInsert', 'type' => 'int unsigned default 1',),
				array( 'column' => 'flagMyDelete', 'type' => 'int unsigned default 1',),
				array( 'column' => 'flagMyUpdate', 'type' => 'int unsigned default 1',),
				array( 'column' => 'flagMyOutput', 'type' => 'int unsigned default 1',),

				array( 'column' => 'flagAllSelect', 'type' => 'int unsigned default 1',),
				array( 'column' => 'flagAllInsert', 'type' => 'int unsigned default 1',),
				array( 'column' => 'flagAllDelete', 'type' => 'int unsigned default 1',),
				array( 'column' => 'flagAllUpdate', 'type' => 'int unsigned default 1',),
				array( 'column' => 'flagAllOutput', 'type' => 'int unsigned default 1',),

				array( 'column' => 'arrSpaceStrTag', 'type' => 'mediumtext',),

				array( 'column' => 'flagDefault', 'type' => 'int default 0',),
	    )
	),
	array(
	    'table' => 'accountingAccess',
	    'db' => 'type=InnoDB',
	    'index' => array(
				array( 'column' => 'id', 'type' => 'int unsigned auto_increment,primary key(id)',),
				//stamp
				array( 'column' => 'stampRegister', 'type' => 'bigint not null',),
				array( 'column' => 'stampUpdate',   'type' => 'bigint not null',),

				array( 'column' => 'idAccess', 'type' => 'int unsigned not null',),

				array( 'column' => 'idEntity', 'type' => 'int unsigned not null',),

				array( 'column' => 'strTitle',     'type' => 'varchar(100)',),

				array( 'column' => 'jsonData', 'type' => 'longtext',),

				array( 'column' => 'arrSpaceStrTag', 'type' => 'mediumtext',),

				array( 'column' => 'flagDefault', 'type' => 'int default 0',),
	    )
	),
	array(
	    'table' => 'accountingEntity',
	    'db'    => 'type=InnoDB',
	    'index' => array(
				array( 'column' => 'id', 'type' => 'int unsigned auto_increment,primary key(id)',),

				//stamp
				array( 'column' => 'stampRegister', 'type' => 'bigint not null',),
				array( 'column' => 'stampUpdate',   'type' => 'bigint not null',),

				array( 'column' => 'strTitle',     'type' => 'varchar(100)',),

				array( 'column' => 'strNation', 'type' => 'varchar(3) default "jpn"',),
				array( 'column' => 'strLang', 'type' => 'varchar(3) default "ja"',),
				array( 'column' => 'strCurrency', 'type' => 'varchar(3) default "JPY"',),

				array( 'column' => 'numFiscalPeriodStart', 'type' => 'int unsigned default 1',),
				array( 'column' => 'numFiscalPeriod', 'type' => 'int unsigned default 1',),
				array( 'column' => 'numFiscalPeriodLock', 'type' => 'int unsigned default 0',),
				array( 'column' => 'flagConfig', 'type' => 'int unsigned default 1',),

				array( 'column' => 'arrSpaceStrTag', 'type' => 'mediumtext',),
	    )
	),
	array(
	    'table' => 'accountingEntityJpn',
	    'db'    => 'type=InnoDB',
	    'index' => array(
				array( 'column' => 'id', 'type' => 'int unsigned auto_increment,primary key(id)',),

				array( 'column' => 'idEntity', 'type' => 'int unsigned not null',),
				array( 'column' => 'numFiscalPeriod', 'type' => 'int unsigned',),

				array( 'column' => 'stampFiscalBeginning', 'type' => 'bigint',),
				array( 'column' => 'numFiscalBeginningYear', 'type' => 'int unsigned',),
				array( 'column' => 'numFiscalBeginningMonth', 'type' => 'int unsigned',),
				array( 'column' => 'numFiscalTermMonth', 'type' => 'int unsigned default 12',),

				//1:法人 2:個人一般 3:個人不動産 4:個人農業
				array( 'column' => 'flagCorporation', 'type' => 'int unsigned default 1',),

				array( 'column' => 'numYearSheet', 'type' => 'int unsigned default 2012',),

				array( 'column' => 'flagCR', 'type' => 'int unsigned',),

				array( 'column' => 'flagSubsidiaryMoney', 'type' => 'int unsigned default 0',),

				array( 'column' => 'flagConsumptionTaxFree', 'type' => 'int unsigned default 1',),


				array( 'column' => 'flagConsumptionTaxGeneralRule', 'type' => 'int unsigned default 1',),

				//1:each 0:proration
				array( 'column' => 'flagConsumptionTaxDeducted', 'type' => 'int unsigned default 1',),

				array( 'column' => 'flagConsumptionTaxIncluding', 'type' => 'int unsigned default 1',),

				//1:floor 2:round  3:ceil
				array( 'column' => 'flagConsumptionTaxCalc', 'type' => 'int unsigned default 1',),

				//1:in 2:out  3:another
				array( 'column' => 'flagConsumptionTaxWithoutCalc', 'type' => 'int unsigned default 1',),

				array( 'column' => 'flagConsumptionTaxBusinessType', 'type' => 'int unsigned default 1',),

				array( 'column' => 'jsonFlag', 'type' => 'longtext',),
	    )
	),


	array(
	    'table' => 'accountingFSJpn',
	    'db'    => 'type=InnoDB',
	    'index' => array(
				array( 'column' => 'id','type' => 'bigint unsigned auto_increment,primary key(id)',),

				array( 'column' => 'stampRegister', 'type' => 'bigint not null',),
				array( 'column' => 'stampUpdate',   'type' => 'bigint not null',),

				array( 'column' => 'idEntity',     'type' => 'int unsigned not null',),
				array( 'column' => 'numFiscalPeriod', 'type' => 'int unsigned',),

				array( 'column' => 'jsonJgaapAccountTitlePL', 'type' => 'longtext',),
				array( 'column' => 'jsonJgaapAccountTitleBS', 'type' => 'longtext',),
				array( 'column' => 'jsonJgaapAccountTitleCR', 'type' => 'longtext',),

				array( 'column' => 'jsonJgaapFSPL', 'type' => 'longtext',),
				array( 'column' => 'jsonJgaapFSBS', 'type' => 'longtext',),
				array( 'column' => 'jsonJgaapFSCR', 'type' => 'longtext',),
				array( 'column' => 'jsonJgaapFSCS', 'type' => 'longtext',),
	    ),
	),
	array(
	    'table' => 'accountingFSValueJpn',
	    'db'    => 'type=InnoDB',
	    'index' => array(
				array( 'column' => 'id','type' => 'bigint unsigned auto_increment,primary key(id)',),

				array( 'column' => 'stampRegister', 'type' => 'bigint not null',),
				array( 'column' => 'stampUpdate',   'type' => 'bigint not null',),

				array( 'column' => 'idEntity',     'type' => 'int unsigned not null',),
				array( 'column' => 'numFiscalPeriod', 'type' => 'int unsigned',),

				/*
					idAccountTitle :{
						'f1' : {
							sumPrev   : 0,
							sumDebit  : 0,
							sumCredit : 0,
							sumNext   : 0,
							varsTax : {
								tax : {sumBody : 0, sumTax : 0},
								tax-Back : {sumBody : 0, sumTax : 0},
								tax-Bad : {sumBody : 0, sumTax : 0},
								tax-Getback : {sumBody : 0, sumTax : 0},
								taxDebit-Purcheses : {sumBody : 0, sumTax : 0},
								taxDebit-Getback-Purcheses : {sumBody : 0, sumTax : 0},
								free : 0,
								...
							},
							varsAdjust : {
								sumPrev   : 0,
								sumDebit  : 0,
								sumCredit : 0,
								sumNext   : 0,
								varsTax : {},
							},
						},
					},
				*/
				array( 'column' => 'jsonJgaapAccountTitlePL', 'type' => 'longtext',),
				array( 'column' => 'jsonJgaapAccountTitleBS', 'type' => 'longtext',),
				array( 'column' => 'jsonJgaapAccountTitleCR', 'type' => 'longtext',),

				array( 'column' => 'jsonJgaapFSPL', 'type' => 'longtext',),
				array( 'column' => 'jsonJgaapFSBS', 'type' => 'longtext',),
				array( 'column' => 'jsonJgaapFSCR', 'type' => 'longtext',),
				array( 'column' => 'jsonJgaapFSCS', 'type' => 'longtext',),

				/*
					'{
						f1 : {
							tax      : 0,
							tax-Back : 0,
							...
						},
					}'
				*/
				array( 'column' => 'jsonConsumptionTax', 'type' => 'longtext',),
	    ),
	),
	array(
	    'table' => 'accountingFSIdJpn',
	    'db'    => 'type=InnoDB',
	    'index' => array(
				array( 'column' => 'id','type' => 'bigint unsigned auto_increment,primary key(id)',),

				array( 'column' => 'stampRegister', 'type' => 'bigint not null',),
				array( 'column' => 'stampUpdate',   'type' => 'bigint not null',),

				array( 'column' => 'idEntity',     'type' => 'int unsigned not null',),

				/*
					'{
						id : 1,
					}'
				*/
				array( 'column' => 'jsonJgaapAccountTitlePL', 'type' => 'longtext',),
				array( 'column' => 'jsonJgaapAccountTitleBS', 'type' => 'longtext',),
				array( 'column' => 'jsonJgaapAccountTitleCR', 'type' => 'longtext',),

				array( 'column' => 'jsonJgaapFSPL', 'type' => 'longtext',),
				array( 'column' => 'jsonJgaapFSBS', 'type' => 'longtext',),
				array( 'column' => 'jsonJgaapFSCR', 'type' => 'longtext',),
				array( 'column' => 'jsonJgaapFSCS', 'type' => 'longtext',),
	    ),
	),
	array(
	    'table' => 'accountingSubAccountTitleJpn',
	    'db'    => 'type=InnoDB',
	    'index' => array(

				array( 'column' => 'id','type' => 'bigint unsigned auto_increment,primary key(id)',),

				array( 'column' => 'stampRegister', 'type' => 'bigint not null',),
				array( 'column' => 'stampUpdate',   'type' => 'bigint not null',),
				array( 'column' => 'idSubAccountTitle', 'type' => 'int unsigned default 0',),
				array( 'column' => 'idEntity',     'type' => 'int unsigned not null',),
				array( 'column' => 'numFiscalPeriod', 'type' => 'int unsigned default 1',),

			array( 'column' => 'idAccountTitle', 'type' => 'varchar(100)',),
				array( 'column' => 'strTitle',     'type' => 'text',),

				array( 'column' => 'arrSpaceStrTag', 'type' => 'mediumtext',),
	    ),
	),
	array(
	    'table' => 'accountingSubAccountTitleValueJpn',
	    'db'    => 'type=InnoDB',
	    'index' => array(
				array( 'column' => 'id', 'type' => 'int unsigned auto_increment,primary key(id)',),

				array( 'column' => 'stampRegister', 'type' => 'bigint not null',),
				array( 'column' => 'stampUpdate',   'type' => 'bigint not null',),

				array( 'column' => 'idSubAccountTitle', 'type' => 'int unsigned not null',),
				array( 'column' => 'idEntity', 'type' => 'int unsigned not null',),
				array( 'column' => 'numFiscalPeriod', 'type' => 'int unsigned default 1',),

				/*
					'all' :{
						'f1' : {
							sumPrev   : 0,
							sumDebit  : 0,
							sumCredit : 0,
							sumNext   : 0,
							varsAdjust : {
								sumPrev   : 0,
								sumDebit  : 0,
								sumCredit : 0,
								sumNext   : 0,
							},
						},
					},
					'idDepartment' :{},
				*/
				array( 'column' => 'jsonData', 'type' => 'longtext',),
	    )
	),
	array(
	    'table' => 'accountingCash',
	    'db'    => 'type=InnoDB',
	    'index' => array(
				array( 'column' => 'id','type' => 'bigint unsigned auto_increment,primary key(id)',),
				array( 'column' => 'stampRegister', 'type' => 'bigint not null',),
				array( 'column' => 'stampUpdate', 'type' => 'bigint not null',),
				array( 'column' => 'idEntity',     'type' => 'int unsigned not null',),
				array( 'column' => 'numFiscalPeriod', 'type' => 'int unsigned default 1',),
				array( 'column' => 'jsonCash', 'type' => 'text',),
				array( 'column' => 'flagPayWrite', 'type' => 'int unsigned default 0',),
				array( 'column' => 'flagAutoImport', 'type' => 'int unsigned default 1',),
				array( 'column' => 'flagPermitImport', 'type' => 'int unsigned default 1',),
	    ),
	),
	array(
	    'table' => 'accountingCashValue',
	    'db'    => 'type=InnoDB',
	    'index' => array(
				array( 'column' => 'id','type' => 'bigint unsigned auto_increment,primary key(id)',),
				array( 'column' => 'stampRegister', 'type' => 'bigint not null',),
				array( 'column' => 'stampUpdate', 'type' => 'bigint not null',),
				array( 'column' => 'idEntity',     'type' => 'int unsigned not null',),
				array( 'column' => 'numFiscalPeriod', 'type' => 'int unsigned default 1',),
				array( 'column' => 'numFiscalPeriodValue', 'type' => 'int unsigned default 1',),
				array( 'column' => 'flagPay',     'type' => 'int default 0',),
				/*
					'{
						f1 : {
							sumIn   : 0,
							...
							varsDetail : {
								idAccountTitle : {
									all : 0,
									idSubAccountTitle : 0,
									...
								},
							},
						},
					}'
				*/
				array( 'column' => 'jsonData', 'type' => 'longtext',),
	    ),
	),
	array(
	    'table' => 'accountingLogCash',
	    'db'    => 'type=InnoDB',
	    'index' => array(
				array( 'column' => 'id','type' => 'bigint unsigned auto_increment,primary key(id)',),

				array( 'column' => 'stampRegister', 'type' => 'bigint not null',),
				array( 'column' => 'stampUpdate',   'type' => 'bigint not null',),
				array( 'column' => 'stampBook',   'type' => 'bigint not null',),
				array( 'column' => 'idLogCash',     'type' => 'bigint unsigned',),
				array( 'column' => 'idEntity',     'type' => 'int unsigned not null',),
				array( 'column' => 'numFiscalPeriod', 'type' => 'int unsigned default 1',),
				array( 'column' => 'idAccount',     'type' => 'int unsigned not null',),

				array( 'column' => 'strTitle',     'type' => 'varchar(100)',),

				array( 'column' => 'arrSpaceStrTag', 'type' => 'mediumtext',),

				array( 'column' => 'flagIn',     'type' => 'int',),

				array( 'column' => 'flagPay',     'type' => 'int default 0',),
	    	array( 'column' => 'stampPay', 'type' => 'bigint default 0',),

				array( 'column' => 'arrCommaIdLogFile', 'type' => 'longtext',),

				/*
					'[ {
						stampRegister    : 0,
						stampUpdate      : 0,
						stampBook        : 0,
						strTitle         : '',
						flagIn           : 0,
						arrSpaceStrTag   : '',
						jsonDetail       : {
							idAccountTitleDebit : '',
							idAccountTitleCredit : '',
							numSum : 0,
							numSumDebit : 0,
							numSumCredit : 0,
							varsEntityNation : {
								flagConsumptionTaxFree : int,
								flagConsumptionTaxGeneralRule : int,
								flagConsumptionTaxDeducted : int,
								flagConsumptionTaxIncluding : int,
							},
							numVersionConsumptionTax : 0,
							varsDetail : [ {
								id : '',
								arrDebit: {
									idAccountTitle : '',
									numValue : '',
									numValeConsumptionTax : '',
									numRateConsumptionTax : '',
									idDepartment : '',
									idSubAccountTitle : '',
									flagConsumptionTaxFree : '',
									flagConsumptionTaxIncluding : '',
									flagConsumptionTaxGeneralRuleProration : '',
									flagConsumptionTaxGeneralRuleEach : '',
									flagConsumptionTaxSimpleRule : '',
									flagConsumptionTaxWithoutCalc : '',
									flagConsumptionTaxCalc : '',
								},
								arrCredit: {

								},
							}],
						},
						arrCommaIdLogFile   : '',
					},]'
				*/
				array( 'column' => 'jsonVersion', 'type' => 'longtext',),

				array( 'column' => 'flagApply', 'type' => 'int unsigned',),
				array( 'column' => 'idAccountApply', 'type' => 'int unsigned',),
				array( 'column' => 'arrCommaIdAccountPermit', 'type' => 'text',),
				array( 'column' => 'numValue', 'type' => 'decimal(19, 0) unsigned',),

				array( 'column' => 'arrCommaIdDepartmentDebit', 'type' => 'longtext',),
				array( 'column' => 'arrCommaIdAccountTitleDebit', 'type' => 'longtext',),
				array( 'column' => 'arrCommaIdSubAccountTitleDebit', 'type' => 'longtext',),
				array( 'column' => 'arrCommaRateConsumptionTaxDebit', 'type' => 'text',),
				array( 'column' => 'arrCommaConsumptionTaxDebit', 'type' => 'longtext',),
				array( 'column' => 'arrCommaConsumptionTaxWithoutCalcDebit', 'type' => 'longtext',),

				array( 'column' => 'arrCommaTaxPaymentDebit', 'type' => 'text',),
				array( 'column' => 'arrCommaTaxReceiptDebit', 'type' => 'text',),

				array( 'column' => 'arrCommaIdDepartmentCredit', 'type' => 'longtext',),
				array( 'column' => 'arrCommaIdAccountTitleCredit', 'type' => 'longtext',),
				array( 'column' => 'arrCommaIdSubAccountTitleCredit', 'type' => 'longtext',),
				array( 'column' => 'arrCommaRateConsumptionTaxCredit', 'type' => 'text',),
				array( 'column' => 'arrCommaConsumptionTaxCredit', 'type' => 'longtext',),
				array( 'column' => 'arrCommaConsumptionTaxWithoutCalcCredit', 'type' => 'longtext',),

				array( 'column' => 'arrCommaTaxPaymentCredit', 'type' => 'text',),
				array( 'column' => 'arrCommaTaxReceiptCredit', 'type' => 'text',),

				/*
					'[ {
						stampRegister : 0,
						idAccount     : 1,
					}]'
				*/
				array( 'column' => 'jsonChargeHistory', 'type' => 'longtext',),
				/*
					'[ {
						flagInvalid        : 0,
						stampRegister      : 0,
						numSumMax          : 0,
						idAccountApply     : 1,
						arrIdAccountPermit : [
							{ stampRegister : 0, idAccount : 0, flagPermit : 'none','done','back' }
						],
					}]'
				*/
				array( 'column' => 'jsonPermitHistory','type' => 'longtext',),
				/*
					'[ {
						stampRegister : 0,
						idAccount     : 1,
						idLog : 0,
					}]'
				*/
				array( 'column' => 'jsonWriteHistory', 'type' => 'longtext',),

				array( 'column' => 'flagRemove', 'type' => 'int unsigned default 0',),
				array( 'column' => 'stampRemove', 'type' => 'bigint default 0',),
	    ),
	),
	array(
	    'table' => 'accountingLogCashDefer',
	    'db'    => 'type=InnoDB',
	    'index' => array(
				array( 'column' => 'id','type' => 'bigint unsigned auto_increment,primary key(id)',),

				array( 'column' => 'stampRegister', 'type' => 'bigint not null',),
				array( 'column' => 'stampUpdate',   'type' => 'bigint not null',),
				array( 'column' => 'stampArrive', 'type' => 'bigint',),
				array( 'column' => 'stampBook',   'type' => 'bigint not null',),

				array( 'column' => 'flagType', 'type' => 'text',),
				array( 'column' => 'numRow', 'type' => 'int unsigned',),

				array( 'column' => 'idEntity',     'type' => 'int unsigned not null',),
				array( 'column' => 'numFiscalPeriod', 'type' => 'int unsigned default 1',),
				array( 'column' => 'idAccount',     'type' => 'int unsigned not null',),

				//f1 f2 f41 f42 f43 0
				array( 'column' => 'flagFiscalReport', 'type' => 'varchar(3)',),

				array( 'column' => 'strTitle',     'type' => 'varchar(100)',),

				array( 'column' => 'arrSpaceStrTag', 'type' => 'mediumtext',),

				array( 'column' => 'flagApply', 'type' => 'int unsigned',),
				array( 'column' => 'idAccountApply', 'type' => 'int unsigned',),
				array( 'column' => 'arrCommaIdAccountPermit', 'type' => 'text',),

				/*
					'[ {
						stampRegister    : 0,
						stampUpdate      : 0,
						stampBook        : 0,
						strTitle         : '',
						flagFiscalReport : 0,
						arrSpaceStrTag   : '',
						jsonDetail       : {
							idAccountTitleDebit : '',
							idAccountTitleCredit : '',
							numSum : 0,
							numSumDebit : 0,
							numSumCredit : 0,
							varsEntityNation : {
								flagConsumptionTaxFree : int,
								flagConsumptionTaxGeneralRule : int,
								flagConsumptionTaxDeducted : int,
								flagConsumptionTaxIncluding : int,
							},
							numVersionConsumptionTax : 0,
							varsDetail : [ {
								id : '',
								arrDebit: {
									idAccountTitle : '',
									numValue : '',
									numValueConsumptionTax : '',
									numRateConsumptionTax : '',
									idDepartment : '',
									idSubAccountTitle : '',
									flagConsumptionTaxFree : '',
									flagConsumptionTaxIncluding : '',
									flagConsumptionTaxGeneralRuleProration : '',
									flagConsumptionTaxGeneralRuleEach : '',
									flagConsumptionTaxSimpleRule : '',
									flagConsumptionTaxWithoutCalc : '',
									flagConsumptionTaxCalc : '',
								},
								arrCredit: {

								},
							}],
						},
						arrCommaIdLogFile   : '',
					},]'
				*/
				array( 'column' => 'jsonVersion', 'type' => 'longtext',),
				array( 'column' => 'numValue', 'type' => 'decimal(19, 0) unsigned',),

				array( 'column' => 'arrCommaIdDepartmentDebit', 'type' => 'longtext',),
				array( 'column' => 'arrCommaIdAccountTitleDebit', 'type' => 'longtext',),
				array( 'column' => 'arrCommaIdSubAccountTitleDebit', 'type' => 'longtext',),
				array( 'column' => 'arrCommaRateConsumptionTaxDebit', 'type' => 'text',),
				array( 'column' => 'arrCommaConsumptionTaxDebit', 'type' => 'longtext',),
				array( 'column' => 'arrCommaConsumptionTaxWithoutCalcDebit', 'type' => 'longtext',),

				array( 'column' => 'arrCommaTaxPaymentDebit', 'type' => 'text',),
				array( 'column' => 'arrCommaTaxReceiptDebit', 'type' => 'text',),

				array( 'column' => 'arrCommaIdDepartmentCredit', 'type' => 'longtext',),
				array( 'column' => 'arrCommaIdAccountTitleCredit', 'type' => 'longtext',),
				array( 'column' => 'arrCommaIdSubAccountTitleCredit', 'type' => 'longtext',),
				array( 'column' => 'arrCommaRateConsumptionTaxCredit', 'type' => 'text',),
				array( 'column' => 'arrCommaConsumptionTaxCredit', 'type' => 'longtext',),
				array( 'column' => 'arrCommaConsumptionTaxWithoutCalcCredit', 'type' => 'longtext',),

				array( 'column' => 'arrCommaTaxPaymentCredit', 'type' => 'text',),
				array( 'column' => 'arrCommaTaxReceiptCredit', 'type' => 'text',),

				array( 'column' => 'arrCommaIdDepartmentVersion', 'type' => 'longtext',),
				array( 'column' => 'arrCommaIdAccountTitleVersion', 'type' => 'longtext',),
				array( 'column' => 'arrCommaIdSubAccountTitleVersion', 'type' => 'longtext',),
				/*
					'[ {
						stampRegister : 0,
						idAccount     : 1,
					}]'
				*/
				array( 'column' => 'jsonChargeHistory', 'type' => 'longtext',),

				/*
					'[ {
						flagInvalid        : 0,
						stampRegister      : 0,
						numSumMax          : 0,
						idAccountApply     : 1,
						arrIdAccountPermit : [
							{ stampRegister : 0, idAccount : 0, flagPermit : 'none','done','back' }
						],
					}]'
				*/
				array( 'column' => 'jsonPermitHistory','type' => 'longtext',),
	    ),
	),
	array(
	    'table' => 'accountingLog',
	    'db'    => 'type=InnoDB',
	    'index' => array(
				array( 'column' => 'id','type' => 'bigint unsigned auto_increment,primary key(id)',),

				array( 'column' => 'stampRegister', 'type' => 'bigint not null',),
				array( 'column' => 'stampUpdate',   'type' => 'bigint not null',),
				array( 'column' => 'stampArrive', 'type' => 'bigint',),
				array( 'column' => 'stampBook',   'type' => 'bigint not null',),
				array( 'column' => 'idLog',     'type' => 'bigint unsigned',),
				array( 'column' => 'idEntity',     'type' => 'int unsigned not null',),
				array( 'column' => 'numFiscalPeriod', 'type' => 'int unsigned default 1',),
				array( 'column' => 'idAccount',     'type' => 'int unsigned not null',),

				//f1 f2 f41 f42 f43 0
				array( 'column' => 'flagFiscalReport', 'type' => 'varchar(3)',),

				array( 'column' => 'strTitle',     'type' => 'varchar(100)',),

				array( 'column' => 'arrSpaceStrTag', 'type' => 'mediumtext',),

				array( 'column' => 'flagApply', 'type' => 'int unsigned',),
				array( 'column' => 'idAccountApply', 'type' => 'int unsigned',),
				array( 'column' => 'flagApplyBack', 'type' => 'int unsigned',),
				array( 'column' => 'arrCommaIdAccountPermit', 'type' => 'text',),

				array( 'column' => 'arrCommaIdLogFile', 'type' => 'longtext',),

				/*
					'[ {
						stampRegister    : 0,
						stampUpdate      : 0,
						stampBook        : 0,
						strTitle         : '',
						flagFiscalReport : 0,
						arrSpaceStrTag   : '',
						jsonDetail       : {
							idAccountTitleDebit : '',
							idAccountTitleCredit : '',
							numSum : 0,
							numSumDebit : 0,
							numSumCredit : 0,
							varsEntityNation : {
								flagConsumptionTaxFree : int,
								flagConsumptionTaxGeneralRule : int,
								flagConsumptionTaxDeducted : int,
								flagConsumptionTaxIncluding : int,
							},
							numVersionConsumptionTax : 0,
							varsDetail : [ {
								id : '',
								arrDebit: {
									idAccountTitle : '',
									numValue : '',
									numValueConsumptionTax : '',
									numRateConsumptionTax : '',
									idDepartment : '',
									idSubAccountTitle : '',
									flagConsumptionTaxFree : '',
									flagConsumptionTaxIncluding : '',
									flagConsumptionTaxGeneralRuleProration : '',
									flagConsumptionTaxGeneralRuleEach : '',
									flagConsumptionTaxSimpleRule : '',
									flagConsumptionTaxWithoutCalc : '',
									flagConsumptionTaxCalc : '',
								},
								arrCredit: {

								},
							}],
						},
						arrCommaIdLogFile   : '',
					},]'
				*/
				array( 'column' => 'jsonVersion', 'type' => 'longtext',),
				array( 'column' => 'numValue', 'type' => 'decimal(19, 0) unsigned',),

				array( 'column' => 'arrCommaIdDepartmentDebit', 'type' => 'longtext',),
				array( 'column' => 'arrCommaIdAccountTitleDebit', 'type' => 'longtext',),
				array( 'column' => 'arrCommaIdSubAccountTitleDebit', 'type' => 'longtext',),
				array( 'column' => 'arrCommaRateConsumptionTaxDebit', 'type' => 'text',),
				array( 'column' => 'arrCommaConsumptionTaxDebit', 'type' => 'longtext',),
				array( 'column' => 'arrCommaConsumptionTaxWithoutCalcDebit', 'type' => 'longtext',),

				array( 'column' => 'arrCommaTaxPaymentDebit', 'type' => 'text',),
				array( 'column' => 'arrCommaTaxReceiptDebit', 'type' => 'text',),

				array( 'column' => 'arrCommaIdDepartmentCredit', 'type' => 'longtext',),
				array( 'column' => 'arrCommaIdAccountTitleCredit', 'type' => 'longtext',),
				array( 'column' => 'arrCommaIdSubAccountTitleCredit', 'type' => 'longtext',),
				array( 'column' => 'arrCommaRateConsumptionTaxCredit', 'type' => 'text',),
				array( 'column' => 'arrCommaConsumptionTaxCredit', 'type' => 'longtext',),
				array( 'column' => 'arrCommaConsumptionTaxWithoutCalcCredit', 'type' => 'longtext',),

				array( 'column' => 'arrCommaTaxPaymentCredit', 'type' => 'text',),
				array( 'column' => 'arrCommaTaxReceiptCredit', 'type' => 'text',),

				array( 'column' => 'arrCommaIdDepartmentVersion', 'type' => 'longtext',),
				array( 'column' => 'arrCommaIdAccountTitleVersion', 'type' => 'longtext',),
				array( 'column' => 'arrCommaIdSubAccountTitleVersion', 'type' => 'longtext',),
				/*
					'[ {
						stampRegister : 0,
						idAccount     : 1,
					}]'
				*/
				array( 'column' => 'jsonChargeHistory', 'type' => 'longtext',),

				/*
					'[ {
						flagInvalid        : 0,
						stampRegister      : 0,
						numSumMax          : 0,
						idAccountApply     : 1,
						arrIdAccountPermit : [
							{ stampRegister : 0, idAccount : 0, flagPermit : 'none','done','back' }
						],
					}]'
				*/
				array( 'column' => 'jsonPermitHistory','type' => 'longtext',),

				array( 'column' => 'flagRemove', 'type' => 'int unsigned default 0',),
				array( 'column' => 'stampRemove', 'type' => 'bigint default 0',),
	    ),
	),
	array(
	    'table' => 'accountingLogMailJpn',
	    'db'    => 'type=InnoDB',
	    'index' => array(
				array( 'column' => 'id', 'type' => 'int unsigned auto_increment,primary key(id)',),

				//stamp
				array( 'column' => 'stampRegister',      'type' => 'bigint not null',),
				array( 'column' => 'stampUpdate',	'type' => 'bigint not null',),

				array( 'column' => 'idEntity',     'type' => 'int unsigned not null',),
				array( 'column' => 'numFiscalPeriod', 'type' => 'int unsigned',),
				//format   '{"a@a.a":1}'
				array( 'column' => 'jsonMail', 'type' => 'longtext',),
				//format   '{"a.a":1}'
				array( 'column' => 'jsonMailHost', 'type' => 'longtext',),

				array( 'column' => 'strHost', 'type' => 'text',),
				array( 'column' => 'strUser', 'type' => 'text',),
				array( 'column' => 'strPassword', 'type' => 'tinyblob',),
				array( 'column' => 'numPort', 'type' => 'varchar(5) default 993',),
				array( 'column' => 'flagSecure', 'type' => 'varchar(5) default "ssl"',),
				array( 'column' => 'strMail', 'type' => 'text',),
	    ),
	),
	array(
	    'table' => 'accountingLogImportJpn',
	    'db'    => 'type=InnoDB',
	    'index' => array(
				array( 'column' => 'id','type' => 'bigint unsigned auto_increment,primary key(id)',),
				array( 'column' => 'stampRegister', 'type' => 'bigint not null',),
				array( 'column' => 'stampUpdate', 'type' => 'bigint not null',),
				array( 'column' => 'idEntity',     'type' => 'int unsigned not null',),
				array( 'column' => 'numFiscalPeriod', 'type' => 'int unsigned default 1',),
				array( 'column' => 'idLogImport',     'type' => 'bigint unsigned',),
				array( 'column' => 'strTitle',     'type' => 'varchar(100)',),
				//eq start end like
				array( 'column' => 'flagAttest',     'type' => 'varchar(5)',),

				array( 'column' => 'flagApply', 'type' => 'int unsigned',),
				array( 'column' => 'idAccountApply', 'type' => 'int unsigned',),
				array( 'column' => 'arrCommaIdAccountPermit', 'type' => 'text',),

				array( 'column' => 'arrCommaIdDepartmentDebit', 'type' => 'longtext',),
				array( 'column' => 'arrCommaIdAccountTitleDebit', 'type' => 'longtext',),
				array( 'column' => 'arrCommaIdSubAccountTitleDebit', 'type' => 'longtext',),
				array( 'column' => 'arrCommaRateConsumptionTaxDebit', 'type' => 'text',),
				array( 'column' => 'arrCommaConsumptionTaxDebit', 'type' => 'longtext',),
				array( 'column' => 'arrCommaConsumptionTaxWithoutCalcDebit', 'type' => 'longtext',),

				array( 'column' => 'arrCommaTaxPaymentDebit', 'type' => 'text',),
				array( 'column' => 'arrCommaTaxReceiptDebit', 'type' => 'text',),

				array( 'column' => 'arrCommaIdDepartmentCredit', 'type' => 'longtext',),
				array( 'column' => 'arrCommaIdAccountTitleCredit', 'type' => 'longtext',),
				array( 'column' => 'arrCommaIdSubAccountTitleCredit', 'type' => 'longtext',),
				array( 'column' => 'arrCommaRateConsumptionTaxCredit', 'type' => 'text',),
				array( 'column' => 'arrCommaConsumptionTaxCredit', 'type' => 'longtext',),
				array( 'column' => 'arrCommaConsumptionTaxWithoutCalcCredit', 'type' => 'longtext',),

				array( 'column' => 'arrCommaTaxPaymentCredit', 'type' => 'text',),
				array( 'column' => 'arrCommaTaxReceiptCredit', 'type' => 'text',),

				array( 'column' => 'jsonVersion', 'type' => 'longtext',),
				/*
					'[ {
						flagInvalid        : 0,
						stampRegister      : 0,
						numSumMax          : 0,
						idAccountApply     : 1,
						arrIdAccountPermit : [
							{ stampRegister : 0, idAccount : 0, flagPermit : 'none','done','back' }
						],
					}]'
				*/
				array( 'column' => 'jsonPermitHistory','type' => 'longtext',),

				array( 'column' => 'numColStampBook',     'type' => 'int default 1',),
				array( 'column' => 'numColNumValue',     'type' => 'int default 2',),
				array( 'column' => 'numColStrTitle',     'type' => 'int default 3',),

				array( 'column' => 'arrSpaceStrTag', 'type' => 'text',),
	    ),
	),
	array(
	    'table' => 'accountingLogImportRetryJpn',
	    'db'    => 'type=InnoDB',
	    'index' => array(
				array( 'column' => 'id','type' => 'bigint unsigned auto_increment,primary key(id)',),
				array( 'column' => 'stampRegister', 'type' => 'bigint not null',),
				array( 'column' => 'stampUpdate', 'type' => 'bigint not null',),

				array( 'column' => 'idAccount',     'type' => 'int unsigned not null',),
				array( 'column' => 'idEntity',     'type' => 'int unsigned not null',),
				array( 'column' => 'numFiscalPeriod', 'type' => 'int unsigned default 1',),
				array( 'column' => 'idLogRetry',     'type' => 'bigint unsigned',),
				//mail item post
				array( 'column' => 'flagType', 'type' => 'text',),
				array( 'column' => 'jsonData', 'type' => 'longtext',),
				array( 'column' => 'arrSpaceStrTag', 'type' => 'mediumtext',),
	    ),
	),
	array(
	    'table' => 'accountingLogCalcJpn',
	    'db'    => 'type=InnoDB',
	    'index' => array(
				array( 'column' => 'id','type' => 'bigint unsigned auto_increment,primary key(id)',),
				array( 'column' => 'stampRegister', 'type' => 'bigint not null',),
				array( 'column' => 'stampBook', 'type' => 'bigint not null',),
				array( 'column' => 'idLog',     'type' => 'bigint unsigned',),
				array( 'column' => 'idEntity',     'type' => 'int unsigned not null',),
				array( 'column' => 'numFiscalPeriod', 'type' => 'int unsigned default 1',),
				array( 'column' => 'idAccount',     'type' => 'int unsigned not null',),
				array( 'column' => 'strTitle',     'type' => 'varchar(100)',),

				//f1 f2 f41 f42 f43 0
				array( 'column' => 'flagFiscalReport', 'type' => 'varchar(3)',),
				array( 'column' => 'flagDebit',     'type' => 'int unsigned',),
				array( 'column' => 'idAccountTitle',     'type' => 'varchar(100)',),
				array( 'column' => 'idDepartment',     'type' => 'int unsigned',),
				array( 'column' => 'idSubAccountTitle',     'type' => 'int unsigned',),

				array( 'column' => 'idAccountTitleContra',     'type' => 'varchar(100)',),
				array( 'column' => 'idDepartmentContra',     'type' => 'int unsigned',),
				array( 'column' => 'idSubAccountTitleContra',     'type' => 'int unsigned',),
				array( 'column' => 'numValue', 'type' => 'decimal(19, 0) unsigned',),

				//科目別消費税集計表
				array( 'column' => 'numValueConsumptionTax', 'type' => 'decimal(19, 0) default 0',),
				array( 'column' => 'numRateConsumptionTax', 'type' => 'int unsigned',),
				array( 'column' => 'flagConsumptionTax', 'type' => 'text',),
				array( 'column' => 'flagConsumptionTaxWithoutCalc', 'type' => 'int unsigned',),

				array( 'column' => 'numBalance', 'type' => 'decimal(19, 0) default 0',),
				array( 'column' => 'numBalanceSubAccount', 'type' => 'decimal(19, 0) default 0',),
				array( 'column' => 'numBalanceDepartment', 'type' => 'decimal(19, 0) default 0',),
				array( 'column' => 'numBalanceDepartmentSubAccount', 'type' => 'decimal(19, 0) default 0',),
	    ),
	),
	array(
	    'table' => 'accountingFile',
	    'db'    => 'type=InnoDB',
	    'index' => array(
				array( 'column' => 'id', 'type' => 'int unsigned auto_increment,primary key(id)',),

				//stamp
				array( 'column' => 'stampRegister',      'type' => 'bigint not null',),
				array( 'column' => 'stampUpdate',	'type' => 'bigint not null',),

				array( 'column' => 'idEntity',     'type' => 'int unsigned not null',),
				array( 'column' => 'numFiscalPeriod', 'type' => 'int unsigned',),
				//format   '{"pdf":1}'
				array( 'column' => 'jsonFileType', 'type' => 'longtext',),

				//format   '{"a@a.a":1}'
				array( 'column' => 'jsonMail', 'type' => 'longtext',),
				//format   '{"a.a":1}'
				array( 'column' => 'jsonMailHost', 'type' => 'longtext',),

				array( 'column' => 'strHost', 'type' => 'text',),
				array( 'column' => 'strUser', 'type' => 'text',),
				array( 'column' => 'strPassword', 'type' => 'tinyblob',),
				array( 'column' => 'numPort', 'type' => 'varchar(5) default 993',),
				array( 'column' => 'flagSecure', 'type' => 'varchar(5) default "ssl"',),
				array( 'column' => 'strMail', 'type' => 'text',),
	    ),
	),
	array(
	    'table' => 'accountingLogFile',
	    'db'    => 'type=InnoDB',
	    'index' => array(
				array( 'column' => 'id','type' => 'bigint unsigned auto_increment,primary key(id)',),

				array( 'column' => 'stampRegister', 'type' => 'bigint not null',),
				array( 'column' => 'stampUpdate', 'type' => 'bigint not null',),
				array( 'column' => 'stampArrive', 'type' => 'bigint',),
				array( 'column' => 'idLogFile',     'type' => 'bigint unsigned',),
				array( 'column' => 'idAccount',     'type' => 'int unsigned not null',),
				array( 'column' => 'idEntity',     'type' => 'int unsigned not null',),
				array( 'column' => 'numFiscalPeriod', 'type' => 'int unsigned default 1',),

				array( 'column' => 'strTitle',     'type' => 'varchar(100)',),
				array( 'column' => 'numByte',      'type' => 'bigint unsigned not null',),
				array( 'column' => 'numWidth',      'type' => 'int unsigned',),
				array( 'column' => 'numHeight',      'type' => 'int unsigned',),
				array( 'column' => 'strUrl',      'type' => 'text',),
				array( 'column' => 'strFileType',     'type' => 'varchar(10) not null',),
				array( 'column' => 'arrSpaceStrTag', 'type' => 'mediumtext',),

				/*
					'[ {
						stampRegister  : 0,
						stampUpdate    : 0,
						strTitle       : '',
						numByte        : '',
						strUrl         : '',
						strFileType    : '',
						arrSpaceStrTag : '',
					},]'
				*/
				array( 'column' => 'jsonVersion', 'type' => 'longtext',),

				/*
					'[ {
						stampRegister : 0,
						idAccount     : 1,
					}]'
				*/
				array( 'column' => 'jsonChargeHistory', 'type' => 'longtext',),
				array( 'column' => 'idAccountUpload',     'type' => 'int unsigned not null',),
				array( 'column' => 'flagRemove', 'type' => 'int unsigned default 0',),
				array( 'column' => 'stampRemove', 'type' => 'bigint default 0',),

	    ),
	),
	array(
	    'table' => 'accountingFixedAssetsJpn',
	    'db'    => 'type=InnoDB',
	    'index' => array(
				array( 'column' => 'id', 'type' => 'int unsigned auto_increment,primary key(id)',),

				array( 'column' => 'stampRegister',      'type' => 'bigint not null',),
				array( 'column' => 'stampUpdate',	'type' => 'bigint not null',),

				array( 'column' => 'idEntity', 'type' => 'int unsigned not null',),
				array( 'column' => 'numFiscalPeriod', 'type' => 'int unsigned',),
				//f1
				array( 'column' => 'flagDepWrite', 'type' => 'varchar(3) default "f1"',),
				array( 'column' => 'flagLossWrite', 'type' => 'int default 0',),
				array( 'column' => 'flagFractionDepWrite', 'type' => 'varchar(5) default "ceil"',),
				array( 'column' => 'flagFractionDep', 'type' => 'varchar(5) default "ceil"',),
				array( 'column' => 'flagFractionDepSurvivalRate', 'type' => 'varchar(5) default "floor"',),
				array( 'column' => 'flagFractionDepSurvivalRateLimit', 'type' => 'varchar(5) default "floor"',),
				array( 'column' => 'flagFractionRatioOperate', 'type' => 'varchar(5) default "ceil"',),

				/*
					'id : {
						flagDep  : 0,
						...
					},]'
				*/
				array( 'column' => 'jsonAccountTitle', 'type' => 'longtext',),

				/*
					'numPeriodStart : {
						'numValueSum' : 0,
						'numValueCompression' : 0,
						'varsDetail'  : [
							'numPeriod' : {
								'numValueDepLimit' : 0,
							},
						]
					...
					},'
				*/
				array( 'column' => 'jsonDepSum', 'type' => 'longtext',),
				array( 'column' => 'numRatioOperateDepSum', 'type' => 'decimal(5, 2) default "100.00"',),
	    )
	),
	array(
	    'table' => 'accountingLogFixedAssetsJpn',
	    'db'    => 'type=InnoDB',
	    'index' => array(
				array( 'column' => 'id','type' => 'int unsigned auto_increment,primary key(id)',),

				array( 'column' => 'stampRegister', 'type' => 'bigint not null',),
				array( 'column' => 'stampUpdate', 'type' => 'bigint not null',),

				array( 'column' => 'idFixedAssets', 'type' => 'int unsigned not null',),
				array( 'column' => 'idAccount',     'type' => 'int unsigned not null',),
				array( 'column' => 'idEntity',     'type' => 'int unsigned not null',),
				array( 'column' => 'numFiscalPeriod', 'type' => 'int unsigned default 1',),

				array( 'column' => 'strTitle',     'type' => 'varchar(100)',),
				array( 'column' => 'idAccountTitle', 'type' => 'varchar(100)',),
				array( 'column' => 'flagDepMethod', 'type' => 'varchar(100)',),
				array( 'column' => 'numUsefulLife', 'type' => 'int',),
				array( 'column' => 'numVolume', 'type' => 'decimal(7, 2) unsigned',),
				array( 'column' => 'flagDepUnit', 'type' => 'varchar(100)',),
				array( 'column' => 'idDepartment', 'type' => 'int unsigned',),
				array( 'column' => 'flagTaxFixed', 'type' => 'varchar(100)',),
				array( 'column' => 'flagTaxFixedType', 'type' => 'varchar(100)',),
				array( 'column' => 'flagDepUp', 'type' => 'varchar(100)',),
				array( 'column' => 'flagDepDown', 'type' => 'varchar(100)',),
				array( 'column' => 'stampBuy', 'type' => 'bigint',),
				array( 'column' => 'stampStart', 'type' => 'bigint not null',),
				array( 'column' => 'stampEnd', 'type' => 'bigint',),
				array( 'column' => 'stampDrop', 'type' => 'bigint',),
				array( 'column' => 'numValue', 'type' => 'decimal(19, 0) unsigned',),
				array( 'column' => 'numValueCompression', 'type' => 'decimal(19, 0) unsigned',),
				array( 'column' => 'numValueNet', 'type' => 'decimal(19, 0) unsigned',),
				array( 'column' => 'numSurvivalRate', 'type' => 'decimal(3, 0) unsigned',),
				array( 'column' => 'numSurvivalRateLimit', 'type' => 'decimal(3, 0) unsigned',),
				array( 'column' => 'numValueRemainingBook', 'type' => 'decimal(19, 0) unsigned',),
				array( 'column' => 'numValueAccumulated', 'type' => 'decimal(19, 0) unsigned',),
				array( 'column' => 'numValueNetOpening', 'type' => 'decimal(19, 0) unsigned',),
				array( 'column' => 'numValueDepCalcBase', 'type' => 'decimal(19, 0) unsigned',),
				array( 'column' => 'numValueDepPrevOver', 'type' => 'decimal(19, 0) unsigned',),
				array( 'column' => 'arrCommaDepMonth', 'type' => 'varchar(28)',),
				array( 'column' => 'numRateDep', 'type' => 'decimal(6, 5) unsigned',),

				/*
					normal : 1,
					update : 0,
				*/
				array( 'column' => 'flagDepRateType','type' => 'int unsigned default 1',),
				array( 'column' => 'numValueAssured','type' => 'decimal(19, 0) unsigned',),
				array( 'column' => 'numValueDepCalc','type' => 'decimal(19, 0) unsigned',),
				array( 'column' => 'numValueDepUp', 'type' => 'decimal(19, 0) unsigned',),
				array( 'column' => 'numValueDepExtra', 'type' => 'decimal(19, 0) unsigned',),
				array( 'column' => 'numValueDepSpecial', 'type' => 'decimal(19, 0) unsigned',),
				array( 'column' => 'numValueDepSpecialShortPrev', 'type' => 'decimal(19, 0) unsigned',),
				array( 'column' => 'numValueDepLimit', 'type' => 'decimal(19, 0) unsigned',),
				array( 'column' => 'numValueDep', 'type' => 'decimal(19, 0) unsigned',),
				array( 'column' => 'numValueAccumulatedClosing', 'type' => 'decimal(19, 0) unsigned',),
				array( 'column' => 'numValueNetClosing', 'type' => 'decimal(19, 0) unsigned',),
				array( 'column' => 'numRatioOperate', 'type' => 'decimal(5, 2) default "100.00"',),
				array( 'column' => 'numValueDepOperate', 'type' => 'decimal(19, 0) unsigned',),
				array( 'column' => 'numValueDepCurrentOver', 'type' => 'decimal(19, 0)',),
				array( 'column' => 'numValueDepNextOver', 'type' => 'decimal(19, 0) unsigned',),
				array( 'column' => 'numValueDepSpecialShortCurrent', 'type' => 'decimal(19, 0) unsigned',),
				array( 'column' => 'numValueDepSpecialShortCurrentCut', 'type' => 'decimal(19, 0) unsigned',),
				array( 'column' => 'numValueDepSpecialShortNext', 'type' => 'decimal(19, 0) unsigned',),
				array( 'column' => 'lossOnDisposalOfFixedAssets', 'type' => 'varchar(100)',),
				array( 'column' => 'accumulatedDepreciation', 'type' => 'varchar(100)',),
				array( 'column' => 'sellingAdminCost', 'type' => 'varchar(100)',),
				array( 'column' => 'productsCost', 'type' => 'varchar(100)',),
				array( 'column' => 'nonOperatingExpenses', 'type' => 'varchar(100)',),
				array( 'column' => 'agricultureCost', 'type' => 'varchar(100)',),
				array( 'column' => 'numRatioSellingAdminCost', 'type' => 'decimal(5, 2) default "100.00"',),
				array( 'column' => 'numRatioProductsCost', 'type' => 'decimal(5, 2) default "0.00"',),
				array( 'column' => 'numRatioNonOperatingExpenses', 'type' => 'decimal(5, 2) default "0.00"',),
				array( 'column' => 'numRatioAgricultureCost', 'type' => 'decimal(5, 2) default "0.00"',),
				array( 'column' => 'flagFraction', 'type' => 'varchar(100)',),
				array( 'column' => 'strMemo',     'type' => 'text',),

				array( 'column' => 'arrSpaceStrTag', 'type' => 'mediumtext',),

				/*
					'[ {
						stampRegister : 0,
						idAccount     : 1,
					}]'
				*/
				array( 'column' => 'jsonChargeHistory', 'type' => 'longtext',),
				/*
					'[ {
						stampRegister : 0,
						idAccount     : 1,
						numFiscalPeriod : 0,
						idLog : 0,
					}]'
				*/
				array( 'column' => 'jsonWriteHistory', 'type' => 'longtext',),
				array( 'column' => 'jsonVersion', 'type' => 'longtext',),

				array( 'column' => 'flagRemove', 'type' => 'int unsigned default 0',),
				array( 'column' => 'stampRemove', 'type' => 'bigint default 0',),

	    ),
	),
	array(
	    'table' => 'accountingBanks',
	    'db'    => 'type=InnoDB',
	    'index' => array(
			array( 'column' => 'id','type' => 'bigint unsigned auto_increment,primary key(id)',),
			array( 'column' => 'stampRegister', 'type' => 'bigint not null',),
			array( 'column' => 'stampUpdate', 'type' => 'bigint not null',),
			array( 'column' => 'idEntity',     'type' => 'int unsigned not null',),
			array( 'column' => 'numFiscalPeriod', 'type' => 'int unsigned default 1',),
			array( 'column' => 'flagAutoImport', 'type' => 'int unsigned default 1',),
			array( 'column' => 'flagLock', 'type' => 'int default 0',),
	    ),
	),
	array(
	    'table' => 'accountingLogBanks',
	    'db'    => 'type=InnoDB',
	    'index' => array(
			array( 'column' => 'id','type' => 'bigint unsigned auto_increment,primary key(id)',),

				array( 'column' => 'stampRegister', 'type' => 'bigint not null',),
				array( 'column' => 'stampUpdate', 'type' => 'bigint not null',),

				array( 'column' => 'idEntity',     'type' => 'int unsigned not null',),
				array( 'column' => 'numFiscalPeriod', 'type' => 'int unsigned default 1',),
	    	array( 'column' => 'idLogBanks', 'type' => 'int unsigned not null',),
	    	array( 'column' => 'idLogAccount', 'type' => 'int unsigned not null',),
	    	array( 'column' => 'idAccount',     'type' => 'int unsigned not null',),

				array( 'column' => 'stampBook', 'type' => 'bigint not null',),
	   		array( 'column' => 'strTitle',     'type' => 'varchar(100)',),
				array( 'column' => 'flagIn', 'type' => 'int',),
				array( 'column' => 'numValueIn', 'type' => 'decimal(19, 0) unsigned',),
				array( 'column' => 'numValueOut', 'type' => 'decimal(19, 0) unsigned',),
				array( 'column' => 'numBalance', 'type' => 'decimal(19, 0)',),
				array( 'column' => 'arrSpaceStrTag', 'type' => 'mediumtext',),

				/*
					'[ {
						stampRegister : 0,
						idAccount     : 1,
					}]'
				*/
				array( 'column' => 'jsonChargeHistory', 'type' => 'longtext',),
				/*
					'[ {
						stampRegister : 0,
						idAccount     : 1,
						numFiscalPeriod : 0,
						idLog : 0,
					}]'
				*/
				array( 'column' => 'jsonWriteHistory', 'type' => 'longtext',),
				array( 'column' => 'jsonVersion', 'type' => 'longtext',),
				array( 'column' => 'flagCaution', 'type' => 'int default 0',),
				array( 'column' => 'flagRemove', 'type' => 'int unsigned default 0',),
				array( 'column' => 'stampRemove', 'type' => 'bigint default 0',),
	    ),
	),
	array(
	    'table' => 'accountingLogBanksAccount',
	    'db'    => 'type=InnoDB',
	    'index' => array(
				array( 'column' => 'id','type' => 'bigint unsigned auto_increment,primary key(id)',),

				//stamp
				array( 'column' => 'stampRegister',      'type' => 'bigint not null',),
				array( 'column' => 'stampUpdate',	'type' => 'bigint not null',),
				array( 'column' => 'idEntity',     'type' => 'int unsigned not null',),
				array( 'column' => 'numFiscalPeriod', 'type' => 'int unsigned',),

				array( 'column' => 'idLogAccount',     'type' => 'int unsigned',),
    		array( 'column' => 'strTitle',     'type' => 'varchar(100)',),
				array( 'column' => 'flagBank', 'type' => 'text',),
				//json
				array( 'column' => 'blobDetail', 'type' => 'blob',),
	    	array( 'column' => 'stampCheck',	'type' => 'bigint',),
				array( 'column' => 'flagLockReason', 'type' => 'text',),
				array( 'column' => 'flagLock', 'type' => 'int default 0',),
				array( 'column' => 'arrSpaceStrTag', 'type' => 'text',),
	    ),
	),
	array(
	    'table' => 'accountingBudgetJpn',
	    'db'    => 'type=InnoDB',
	    'index' => array(
				array( 'column' => 'id','type' => 'bigint unsigned auto_increment,primary key(id)',),

				array( 'column' => 'stampRegister', 'type' => 'bigint not null',),
				array( 'column' => 'stampUpdate', 'type' => 'bigint not null',),
				array( 'column' => 'idEntity',     'type' => 'int unsigned not null',),
				array( 'column' => 'numFiscalPeriod', 'type' => 'int unsigned default 1',),

				//f1 f21 f22 f2sum f41 f42 f43 f44 f4sum msum 1 2 3 4 5 6 7 8 9 10 11 12
				array( 'column' => 'flagFiscalPeriod',     'type' => 'varchar(5)',),

				array( 'column' => 'idDepartment', 'type' => 'int unsigned',),

				//PL CR
				array( 'column' => 'flagFS',     'type' => 'varchar(2)',),

				array( 'column' => 'jsonData', 'type' => 'longtext',),
	    ),
	),
	array(
	    'table' => 'accountingBreakEvenPointJpn',
	    'db'    => 'type=InnoDB',
	    'index' => array(
				array( 'column' => 'id','type' => 'bigint unsigned auto_increment,primary key(id)',),

				array( 'column' => 'stampRegister', 'type' => 'bigint not null',),
				array( 'column' => 'stampUpdate', 'type' => 'bigint not null',),
				array( 'column' => 'idEntity',     'type' => 'int unsigned not null',),
				array( 'column' => 'numFiscalPeriod', 'type' => 'int unsigned default 1',),

				array( 'column' => 'idDepartment', 'type' => 'int unsigned',),

				/*
					'idAccountTitle {
						flagType : '', none sales variable fixed
					},'
				*/
				array( 'column' => 'jsonJgaapAccountTitlePL', 'type' => 'longtext',),
				array( 'column' => 'jsonJgaapAccountTitleCR', 'type' => 'longtext',),
	    ),
	),
	array(
	    'table' => 'accountingSummaryStatementJpn',
	    'db'    => 'type=InnoDB',
	    'index' => array(
				array( 'column' => 'id','type' => 'bigint unsigned auto_increment,primary key(id)',),

				array( 'column' => 'stampRegister', 'type' => 'bigint not null',),
				array( 'column' => 'stampUpdate', 'type' => 'bigint not null',),
				array( 'column' => 'idEntity',     'type' => 'int unsigned not null',),
				array( 'column' => 'numFiscalPeriod', 'type' => 'int unsigned default 1',),
				array( 'column' => 'flagReport', 'type' => 'varchar(100)',),
				array( 'column' => 'flagDetail',    'type' => 'varchar(100)',),
				array( 'column' => 'jsonJgaapAccountTitleBS', 'type' => 'longtext',),
				array( 'column' => 'jsonJgaapAccountTitlePL', 'type' => 'longtext',),
				array( 'column' => 'jsonJgaapAccountTitleCR', 'type' => 'longtext',),

				/*
					'17': {
						id : '',
					},'
				*/
				array( 'column' => 'jsonData', 'type' => 'longtext',),
	    ),
	),

	array(
	    'table' => 'accountingNotesFSJpn',
	    'db'    => 'type=InnoDB',
	    'index' => array(
				array( 'column' => 'id','type' => 'bigint unsigned auto_increment,primary key(id)',),

				array( 'column' => 'stampRegister', 'type' => 'bigint not null',),
				array( 'column' => 'stampUpdate', 'type' => 'bigint not null',),
				array( 'column' => 'idEntity',     'type' => 'int unsigned not null',),
				array( 'column' => 'numFiscalPeriod', 'type' => 'int unsigned default 1',),

				array( 'column' => 'strComment', 'type' => 'text',),
	    ),
	),
	array(
	    'table' => 'accountingDetailedAccountJpn',
	    'db'    => 'type=InnoDB',
	    'index' => array(
				array( 'column' => 'id','type' => 'bigint unsigned auto_increment,primary key(id)',),

				array( 'column' => 'stampRegister', 'type' => 'bigint not null',),
				array( 'column' => 'stampUpdate', 'type' => 'bigint not null',),
				array( 'column' => 'idEntity',     'type' => 'int unsigned not null',),
				array( 'column' => 'numFiscalPeriod', 'type' => 'int unsigned default 1',),
				array( 'column' => 'flagReport', 'type' => 'varchar(100)',),
				array( 'column' => 'flagDetail',    'type' => 'varchar(100)',),
				array( 'column' => 'numPage', 'type' => 'int unsigned default 1',),
				array( 'column' => 'jsonData', 'type' => 'longtext',),
	    ),
	),
	array(
	    'table' => 'accountingLogHouseJpn',
	    'db'    => 'type=InnoDB',
	    'index' => array(
				array( 'column' => 'id','type' => 'bigint unsigned auto_increment,primary key(id)',),
				array( 'column' => 'stampRegister', 'type' => 'bigint not null',),
				array( 'column' => 'stampUpdate', 'type' => 'bigint not null',),
				array( 'column' => 'idEntity',     'type' => 'int unsigned not null',),
				array( 'column' => 'numFiscalPeriod', 'type' => 'int unsigned default 1',),
				array( 'column' => 'idLogHouse',     'type' => 'bigint unsigned',),
				array( 'column' => 'strTitle',     'type' => 'varchar(100)',),

				array( 'column' => 'numRatio', 'type' => 'decimal(5, 2) default "100.00"',),

				array( 'column' => 'flagApply', 'type' => 'int unsigned',),
				array( 'column' => 'idAccountApply', 'type' => 'int unsigned',),
				array( 'column' => 'arrCommaIdAccountPermit', 'type' => 'text',),

				array( 'column' => 'arrCommaIdDepartmentDebit', 'type' => 'longtext',),
				array( 'column' => 'arrCommaIdAccountTitleDebit', 'type' => 'longtext',),
				array( 'column' => 'arrCommaIdSubAccountTitleDebit', 'type' => 'longtext',),
				array( 'column' => 'arrCommaRateConsumptionTaxDebit', 'type' => 'text',),
				array( 'column' => 'arrCommaConsumptionTaxDebit', 'type' => 'longtext',),
				array( 'column' => 'arrCommaConsumptionTaxWithoutCalcDebit', 'type' => 'longtext',),

				array( 'column' => 'arrCommaTaxPaymentDebit', 'type' => 'text',),
				array( 'column' => 'arrCommaTaxReceiptDebit', 'type' => 'text',),

				array( 'column' => 'arrCommaIdDepartmentCredit', 'type' => 'longtext',),
				array( 'column' => 'arrCommaIdAccountTitleCredit', 'type' => 'longtext',),
				array( 'column' => 'arrCommaIdSubAccountTitleCredit', 'type' => 'longtext',),
				array( 'column' => 'arrCommaRateConsumptionTaxCredit', 'type' => 'text',),
				array( 'column' => 'arrCommaConsumptionTaxCredit', 'type' => 'longtext',),
				array( 'column' => 'arrCommaConsumptionTaxWithoutCalcCredit', 'type' => 'longtext',),

				array( 'column' => 'arrCommaTaxPaymentCredit', 'type' => 'text',),
				array( 'column' => 'arrCommaTaxReceiptCredit', 'type' => 'text',),

				array( 'column' => 'jsonVersion', 'type' => 'longtext',),
				/*
					'[ {
						flagInvalid        : 0,
						stampRegister      : 0,
						numSumMax          : 0,
						idAccountApply     : 1,
						arrIdAccountPermit : [
							{ stampRegister : 0, idAccount : 0, flagPermit : 'none','done','back' }
						],
					}]'
				*/
				array( 'column' => 'jsonPermitHistory','type' => 'longtext',),
				array( 'column' => 'arrSpaceStrTag', 'type' => 'text',),

				/*
					'[ {
						stampRegister : 0,
						idAccount     : 1,
						idLog : 0,
					}]'
				*/
				array( 'column' => 'jsonWriteHistory', 'type' => 'longtext',),
	    ),
	),
	array(
	    'table' => 'accountingBlueSheetJpn',
	    'db'    => 'type=InnoDB',
	    'index' => array(
				array( 'column' => 'id','type' => 'bigint unsigned auto_increment,primary key(id)',),

				array( 'column' => 'stampRegister', 'type' => 'bigint not null',),
				array( 'column' => 'stampUpdate', 'type' => 'bigint not null',),
				array( 'column' => 'idEntity',     'type' => 'int unsigned not null',),
				array( 'column' => 'numFiscalPeriod', 'type' => 'int unsigned default 1',),
				
				//2014
				array( 'column' => 'numYearSheet', 'type' => 'int unsigned not null',),

				/*
					'0': {
						id : '',
					},'
				*/
				array( 'column' => 'blobData', 'type' => 'blob',),
	    ),
	),
);

