-- Migration 0001: Initial schema
-- Derived from back/tpl/templates/else/core/base/db/config.php
--   and back/tpl/templates/else/plugin/accounting/db/config.php
-- Compatible with MariaDB 10 (InnoDB, UTF-8).
--
-- NOTE: MariaDB-specific types (e.g. longtext, mediumtext, tinyblob) are used
--       here as this migration targets production MariaDB only.
--       Integration tests use SQLite-compatible migrations (see test helpers).

-- ============================================================
-- Core (base) tables
-- ============================================================

CREATE TABLE IF NOT EXISTS basePreference (
    id               BIGINT UNSIGNED AUTO_INCREMENT,
    stampRegister    BIGINT NOT NULL,
    stampUpdate      BIGINT NOT NULL,
    jsonStampUpdate  MEDIUMTEXT,
    flagMaintenance  TINYINT UNSIGNED DEFAULT 0,
    arrCommaIdAccountMaintenance MEDIUMTEXT,
    numTimeZone      INT,
    strTopUrl        TEXT,
    numAutoLock      INT UNSIGNED DEFAULT 3,
    numPasswordLimit VARCHAR(7) DEFAULT '0',
    numPassword      INT DEFAULT 4,
    arrCommaLockAccount MEDIUMTEXT,
    flagLoginMail    TINYINT UNSIGNED DEFAULT 0,
    flagAccessUnknownMail TINYINT UNSIGNED DEFAULT 0,
    flagLoginSecond  TINYINT UNSIGNED DEFAULT 0,
    flagVersionUpdate TINYINT UNSIGNED DEFAULT 0,
    strSiteName      TEXT NOT NULL,
    strSiteUrl       TEXT,
    strSiteMailPc    TEXT NOT NULL,
    numAutoMustLogout INT UNSIGNED DEFAULT 0,
    flagForgot       TINYINT UNSIGNED DEFAULT 0,
    flagSign         TINYINT UNSIGNED DEFAULT 0,
    jsonIpAccessAccept MEDIUMTEXT,
    jsonIpSubnetAccessAccept MEDIUMTEXT,
    flagReject       TINYINT UNSIGNED DEFAULT 1,
    jsonIpAccessReject MEDIUMTEXT,
    jsonIpSubnetAccessReject MEDIUMTEXT,
    jsonIpSignReject MEDIUMTEXT,
    jsonIpSubnetSignReject MEDIUMTEXT,
    jsonMailSignReject MEDIUMTEXT,
    jsonMailHostSignReject MEDIUMTEXT,
    jsonModule       MEDIUMTEXT,
    strVersion       VARCHAR(11),
    jsonVersion      MEDIUMTEXT,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS baseAccount (
    id               BIGINT UNSIGNED AUTO_INCREMENT,
    stampRegister    BIGINT NOT NULL,
    stampUpdate      BIGINT NOT NULL,
    flagLock         TINYINT UNSIGNED DEFAULT 0,
    flagWebmaster    TINYINT UNSIGNED DEFAULT 0,
    strCodeName      VARCHAR(100) NOT NULL,
    idLogin          TEXT NOT NULL,
    strPassword      TEXT NOT NULL,
    stampUpdatePassword BIGINT,
    strMailPc        TEXT NOT NULL,
    flagLoginMail    TINYINT UNSIGNED DEFAULT 0,
    flagLoginSecond  TINYINT UNSIGNED DEFAULT 0,
    strMailMobile    TEXT,
    idMobile         TEXT,
    strMobileCarrier VARCHAR(100),
    numTimeZone      INT,
    strLang          VARCHAR(2),
    strHoliday       VARCHAR(2),
    numList          INT UNSIGNED DEFAULT 25,
    numAutoLogout    INT UNSIGNED DEFAULT 0,
    numAutoPopup     INT UNSIGNED DEFAULT 0,
    strAutoBoot      VARCHAR(100) DEFAULT 'base',
    idTerm           BIGINT UNSIGNED,
    idModule         BIGINT UNSIGNED,
    arrSpaceStrTag   MEDIUMTEXT,
    jsonStampCheck   MEDIUMTEXT,
    flagDefault      INT DEFAULT 0,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS baseSession (
    stampRegister    BIGINT NOT NULL,
    ip               VARCHAR(15) NOT NULL,
    idCookie         VARCHAR(100) NOT NULL,
    idMobile         TEXT,
    idAccount        BIGINT UNSIGNED NOT NULL,
    flagAPI          INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS baseToken (
    stampRegister    BIGINT NOT NULL,
    token            VARCHAR(100) NOT NULL,
    idAccount        BIGINT UNSIGNED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Accounting tables
-- ============================================================

CREATE TABLE IF NOT EXISTS accountingEntity (
    id               INT UNSIGNED AUTO_INCREMENT,
    stampRegister    BIGINT NOT NULL,
    stampUpdate      BIGINT NOT NULL,
    strTitle         VARCHAR(100),
    strNation        VARCHAR(3) DEFAULT 'jpn',
    strLang          VARCHAR(3) DEFAULT 'ja',
    strCurrency      VARCHAR(3) DEFAULT 'JPY',
    numFiscalPeriodStart INT UNSIGNED DEFAULT 1,
    numFiscalPeriod  INT UNSIGNED DEFAULT 1,
    numFiscalPeriodLock INT UNSIGNED DEFAULT 0,
    flagConfig       INT UNSIGNED DEFAULT 1,
    arrSpaceStrTag   MEDIUMTEXT,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS accountingEntityJpn (
    id               INT UNSIGNED AUTO_INCREMENT,
    idEntity         INT UNSIGNED NOT NULL,
    numFiscalPeriod  INT UNSIGNED,
    stampFiscalBeginning BIGINT,
    numFiscalBeginningYear INT UNSIGNED,
    numFiscalBeginningMonth INT UNSIGNED,
    numFiscalTermMonth INT UNSIGNED DEFAULT 12,
    flagCorporation  INT UNSIGNED DEFAULT 1,
    numYearSheet     INT UNSIGNED DEFAULT 2012,
    flagCR           INT UNSIGNED,
    flagSubsidiaryMoney INT UNSIGNED DEFAULT 0,
    flagConsumptionTaxFree INT UNSIGNED DEFAULT 1,
    flagConsumptionTaxGeneralRule INT UNSIGNED DEFAULT 1,
    flagConsumptionTaxDeducted INT UNSIGNED DEFAULT 1,
    flagConsumptionTaxIncluding INT UNSIGNED DEFAULT 1,
    flagConsumptionTaxCalc INT UNSIGNED DEFAULT 1,
    flagConsumptionTaxWithoutCalc INT UNSIGNED DEFAULT 1,
    flagConsumptionTaxBusinessType INT UNSIGNED DEFAULT 1,
    jsonFlag         LONGTEXT,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS accountingFSJpn (
    id               BIGINT UNSIGNED AUTO_INCREMENT,
    stampRegister    BIGINT NOT NULL,
    stampUpdate      BIGINT NOT NULL,
    idEntity         INT UNSIGNED NOT NULL,
    numFiscalPeriod  INT UNSIGNED,
    jsonJgaapAccountTitlePL LONGTEXT,
    jsonJgaapAccountTitleBS LONGTEXT,
    jsonJgaapAccountTitleCR LONGTEXT,
    jsonJgaapFSPL    LONGTEXT,
    jsonJgaapFSBS    LONGTEXT,
    jsonJgaapFSCR    LONGTEXT,
    jsonJgaapFSCS    LONGTEXT,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS accountingFSValueJpn (
    id               BIGINT UNSIGNED AUTO_INCREMENT,
    stampRegister    BIGINT NOT NULL,
    stampUpdate      BIGINT NOT NULL,
    idEntity         INT UNSIGNED NOT NULL,
    numFiscalPeriod  INT UNSIGNED,
    jsonJgaapAccountTitlePL LONGTEXT,
    jsonJgaapAccountTitleBS LONGTEXT,
    jsonJgaapAccountTitleCR LONGTEXT,
    jsonJgaapFSPL    LONGTEXT,
    jsonJgaapFSBS    LONGTEXT,
    jsonJgaapFSCR    LONGTEXT,
    jsonJgaapFSCS    LONGTEXT,
    jsonConsumptionTax LONGTEXT,
    jsonConsumptionTaxDetail LONGTEXT,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS accountingLog (
    id               BIGINT UNSIGNED AUTO_INCREMENT,
    stampRegister    BIGINT NOT NULL,
    stampUpdate      BIGINT NOT NULL,
    stampArrive      BIGINT,
    stampBook        BIGINT NOT NULL,
    idLog            BIGINT UNSIGNED,
    idEntity         INT UNSIGNED NOT NULL,
    numFiscalPeriod  INT UNSIGNED DEFAULT 1,
    idAccount        INT UNSIGNED NOT NULL,
    flagFiscalReport VARCHAR(3),
    strTitle         VARCHAR(100),
    arrSpaceStrTag   MEDIUMTEXT,
    flagApply        INT UNSIGNED,
    idAccountApply   INT UNSIGNED,
    flagApplyBack    INT UNSIGNED,
    arrCommaIdAccountPermit TEXT,
    arrCommaIdLogFile LONGTEXT,
    jsonVersion      LONGTEXT,
    numValue         DECIMAL(19,0) UNSIGNED,
    arrCommaIdDepartmentDebit LONGTEXT,
    arrCommaIdAccountTitleDebit LONGTEXT,
    arrCommaIdSubAccountTitleDebit LONGTEXT,
    arrCommaRateConsumptionTaxDebit TEXT,
    arrCommaConsumptionTaxDebit LONGTEXT,
    arrCommaConsumptionTaxWithoutCalcDebit LONGTEXT,
    arrCommaTaxPaymentDebit TEXT,
    arrCommaTaxReceiptDebit TEXT,
    arrCommaIdDepartmentCredit LONGTEXT,
    arrCommaIdAccountTitleCredit LONGTEXT,
    arrCommaIdSubAccountTitleCredit LONGTEXT,
    arrCommaRateConsumptionTaxCredit TEXT,
    arrCommaConsumptionTaxCredit LONGTEXT,
    arrCommaConsumptionTaxWithoutCalcCredit LONGTEXT,
    arrCommaTaxPaymentCredit TEXT,
    arrCommaTaxReceiptCredit TEXT,
    jsonChargeHistory LONGTEXT,
    jsonPermitHistory LONGTEXT,
    jsonWriteHistory  LONGTEXT,
    flagRemove        INT UNSIGNED DEFAULT 0,
    stampRemove       BIGINT DEFAULT 0,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS accountingLogCalcJpn (
    id               BIGINT UNSIGNED AUTO_INCREMENT,
    stampRegister    BIGINT NOT NULL,
    stampBook        BIGINT NOT NULL,
    idLog            BIGINT UNSIGNED,
    idEntity         INT UNSIGNED NOT NULL,
    numFiscalPeriod  INT UNSIGNED DEFAULT 1,
    idAccount        INT UNSIGNED NOT NULL,
    strTitle         VARCHAR(100),
    flagFiscalReport VARCHAR(3),
    flagDebit        INT UNSIGNED,
    idAccountTitle   VARCHAR(100),
    idDepartment     INT UNSIGNED,
    idSubAccountTitle INT UNSIGNED,
    idAccountTitleContra VARCHAR(100),
    idDepartmentContra INT UNSIGNED,
    idSubAccountTitleContra INT UNSIGNED,
    numValue         DECIMAL(19,0) UNSIGNED,
    flagRateConsumptionTaxReduced INT UNSIGNED DEFAULT 0,
    numValueConsumptionTax DECIMAL(19,0) DEFAULT 0,
    numRateConsumptionTax INT UNSIGNED,
    flagConsumptionTax TEXT,
    flagConsumptionTaxWithoutCalc INT UNSIGNED,
    numBalance       DECIMAL(19,0) DEFAULT 0,
    numBalanceSubAccount DECIMAL(19,0) DEFAULT 0,
    numBalanceDepartment DECIMAL(19,0) DEFAULT 0,
    numBalanceDepartmentSubAccount DECIMAL(19,0) DEFAULT 0,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS accountingSubAccountTitleJpn (
    id               BIGINT UNSIGNED AUTO_INCREMENT,
    stampRegister    BIGINT NOT NULL,
    stampUpdate      BIGINT NOT NULL,
    idSubAccountTitle INT UNSIGNED DEFAULT 0,
    idEntity         INT UNSIGNED NOT NULL,
    numFiscalPeriod  INT UNSIGNED DEFAULT 1,
    idAccountTitle   VARCHAR(100),
    strTitle         TEXT,
    arrSpaceStrTag   MEDIUMTEXT,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS accountingFixedAssetsJpn (
    id               INT UNSIGNED AUTO_INCREMENT,
    stampRegister    BIGINT NOT NULL,
    stampUpdate      BIGINT NOT NULL,
    idEntity         INT UNSIGNED NOT NULL,
    numFiscalPeriod  INT UNSIGNED,
    flagDepWrite     VARCHAR(3) DEFAULT 'f1',
    flagLossWrite    INT DEFAULT 0,
    flagFractionDepWrite VARCHAR(5) DEFAULT 'ceil',
    flagFractionDep  VARCHAR(5) DEFAULT 'ceil',
    flagFractionDepSurvivalRate VARCHAR(5) DEFAULT 'floor',
    flagFractionDepSurvivalRateLimit VARCHAR(5) DEFAULT 'floor',
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS accountingLogFixedAssetsJpn (
    id               BIGINT UNSIGNED AUTO_INCREMENT,
    stampRegister    BIGINT NOT NULL,
    stampUpdate      BIGINT NOT NULL,
    idLogFixedAssets BIGINT UNSIGNED,
    idEntity         INT UNSIGNED NOT NULL,
    numFiscalPeriod  INT UNSIGNED DEFAULT 1,
    idAccount        INT UNSIGNED NOT NULL,
    strTitle         VARCHAR(100),
    numSurvivalRate  DECIMAL(5,2) DEFAULT 0,
    stampStart       BIGINT,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- schema_migrations tracking table (managed by MigrationRunner)
CREATE TABLE IF NOT EXISTS schema_migrations (
    version    VARCHAR(16) NOT NULL,
    applied_at BIGINT NOT NULL,
    PRIMARY KEY (version)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
