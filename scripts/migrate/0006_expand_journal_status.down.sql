-- Rollback: revert journal_entries.status CHECK to the original 5 values.
-- NOTE: rows with reversed / voided / canceled status will violate the
-- restored constraint, so this rollback is destructive unless those rows
-- are manually cleaned up first.

SET NAMES utf8mb4;

ALTER TABLE journal_entries
    DROP CONSTRAINT chk_journal__status;

ALTER TABLE journal_entries
    ADD CONSTRAINT chk_journal__status CHECK (
        status IN ('draft', 'pending_approval', 'approved', 'rejected', 'posted')
    );
