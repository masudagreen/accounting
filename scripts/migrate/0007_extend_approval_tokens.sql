-- =============================================================
-- Rucaro Accounting - 0007: Extend approval_tokens for Phase 5.2
-- =============================================================
-- 0004 で作成した approval_tokens を、Phase 5 の実装に合わせて拡張する。
--   - token_prefix       : 運用ダッシュボード向けの平文先頭 16 char
--   - target_kind        : journal / receipt のどちらに紐づくかを明示
--   - issued_by_user_id  : 発行者（Bearer で認証された現在ユーザ）
--
-- NOTE: journal_entry_id / receipt_id の FK は 0004 のまま残し、
--       target_kind と組み合わせて Domain 層で整合を取る（ADR-007 strangler）
-- =============================================================

SET NAMES utf8mb4;

ALTER TABLE approval_tokens
    ADD COLUMN token_prefix      VARCHAR(16)  NOT NULL DEFAULT '' AFTER token_hash,
    ADD COLUMN target_kind       VARCHAR(16)  NOT NULL DEFAULT 'journal' AFTER receipt_id,
    ADD COLUMN issued_by_user_id BINARY(16)   NULL DEFAULT NULL AFTER recipient,
    ADD KEY idx_approval_tokens__prefix (token_prefix),
    ADD KEY idx_approval_tokens__kind_target (target_kind, journal_entry_id, receipt_id),
    ADD CONSTRAINT chk_approval_tokens__kind
        CHECK (target_kind IN ('journal', 'receipt'));
