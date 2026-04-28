-- Migration 0002 rollback: Remove invoice columns

ALTER TABLE accountingLogCash
    DROP COLUMN IF EXISTS strInvoiceRegistrationNumber,
    DROP COLUMN IF EXISTS flagInvoiceEligible;

ALTER TABLE accountingLog
    DROP COLUMN IF EXISTS strInvoiceRegistrationNumber,
    DROP COLUMN IF EXISTS flagInvoiceEligible;
