-- Migration 0002: Add invoice (インボイス制度) related columns
-- See docs/ai/06_known_issues.md B-3: Invoice system (2023/10/01~)
--
-- Adds:
--   accountingLog.strInvoiceRegistrationNumber  -- 適格請求書発行事業者番号 (T + 13 digits)
--   accountingLog.flagInvoiceEligible            -- 0=non-eligible, 1=eligible issuer
--
-- These columns allow recording the invoice registration number per journal entry.
-- The transition relief (80%→50%→0% deduction) for non-eligible suppliers is
-- handled at the application layer using these flags.

ALTER TABLE accountingLog
    ADD COLUMN strInvoiceRegistrationNumber VARCHAR(14) DEFAULT NULL COMMENT '適格請求書発行事業者登録番号 T+13桁',
    ADD COLUMN flagInvoiceEligible TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT '1=適格/0=非適格';

ALTER TABLE accountingLogCash
    ADD COLUMN strInvoiceRegistrationNumber VARCHAR(14) DEFAULT NULL COMMENT '適格請求書発行事業者登録番号 T+13桁',
    ADD COLUMN flagInvoiceEligible TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT '1=適格/0=非適格';
