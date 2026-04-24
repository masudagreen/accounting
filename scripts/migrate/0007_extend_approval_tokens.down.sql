-- Rollback for 0007_extend_approval_tokens.sql
ALTER TABLE approval_tokens
    DROP CONSTRAINT chk_approval_tokens__kind,
    DROP KEY idx_approval_tokens__kind_target,
    DROP KEY idx_approval_tokens__prefix,
    DROP COLUMN issued_by_user_id,
    DROP COLUMN target_kind,
    DROP COLUMN token_prefix;
