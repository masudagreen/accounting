-- 0019_users_role.sql
--
-- Per-user role for the journal-approval workflow gate.
--
-- Renewal is single-operator today (the owner of the entity is also the
-- bookkeeper) so existing rows default to `admin`. The non-admin role
-- exists so that, when a future shared deployment lands, the form-level
-- 「承認スキップ」 toggle can be locked off and the post / delete UC
-- guards can deny mutating posted journals for clerks.
ALTER TABLE users
    ADD COLUMN role VARCHAR(16) NOT NULL DEFAULT 'admin' AFTER is_active;

-- The default backfills existing rows; make it explicit for clarity.
UPDATE users SET role = 'admin' WHERE role = '' OR role IS NULL;
