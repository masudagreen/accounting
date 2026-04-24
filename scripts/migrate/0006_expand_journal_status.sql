-- =============================================================
-- Rucaro Accounting - 0006: Expand journal_entries.status values
-- =============================================================
-- Phase 4.2 で Journal 集約に JournalStatus::Reversed / Voided が追加。
-- 0003 の CHECK 制約が 5 値のみだったため、reversed / voided / canceled を
-- 受け入れるように置き換える。既存データ（5 値のいずれか）は影響なし。
-- =============================================================

SET NAMES utf8mb4;

ALTER TABLE journal_entries
    DROP CONSTRAINT chk_journal__status;

ALTER TABLE journal_entries
    ADD CONSTRAINT chk_journal__status CHECK (
        status IN (
            'draft',
            'pending_approval',
            'approved',
            'rejected',
            'posted',
            'reversed',
            'voided',
            'canceled'
        )
    );
