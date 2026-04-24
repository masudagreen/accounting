<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\Import\LegacyImport;

use PDO;
use Rucaro\Infrastructure\Auth\PasswordHasher;

/**
 * Import rows from legacy `baseAccount` into new `users`.
 *
 * Crypto note: legacy `strPassword` is an opaque ciphertext whose scheme
 * (and key file) we cannot reliably reproduce. Per the migration ADR we
 * reset every migrated user to a single placeholder password which must
 * be rotated immediately after cut-over.
 */
final class LegacyUserImporter
{
    public function __construct(
        private readonly PDO $source,
        private readonly PDO $target,
        private readonly IdMapping $idMap,
        private readonly PasswordHasher $hasher,
        private readonly string $placeholderPassword,
        private readonly bool $dryRun,
    ) {
    }

    public function run(): ImportReport
    {
        $rows = $this->source->query(
            'SELECT id, stampRegister, stampUpdate, strCodeName, idLogin, strMailPc, flagLock
               FROM baseAccount
              ORDER BY id'
        );
        if ($rows === false) {
            return ImportReport::empty('users', ['source query failed']);
        }

        $read = 0;
        $inserted = 0;
        $skipped = 0;
        /** @var list<string> $notes */
        $notes = [];

        // Single hash shared across all migrated users; Argon2id is expensive.
        $hash = $this->hasher->hash($this->placeholderPassword);

        $insert = $this->target->prepare(
            'INSERT INTO users
                (id, login_id, display_name, email, password_hash,
                 is_active, created_at, updated_at)
             VALUES
                (:id, :login, :name, :email, :pwh, :active, :ca, :ua)'
        );

        foreach ($rows as $r) {
            ++$read;
            /** @var array<string,mixed> $r */
            $legacyId = (int) $r['id'];
            $legacyLogin = trim((string) $r['idLogin']);
            $legacyName = trim((string) $r['strCodeName']);
            $legacyMail = trim((string) $r['strMailPc']);
            $stampRegister = (int) $r['stampRegister'];
            $stampUpdate = (int) $r['stampUpdate'];
            $flagLock = (int) ($r['flagLock'] ?? 0);

            if ($legacyLogin === '' || $legacyMail === '') {
                ++$skipped;
                $notes[] = sprintf('user#%d skipped (empty login or email)', $legacyId);
                continue;
            }

            $binaryUlid = $this->idMap->getOrCreate(IdMapping::TABLE_USERS, $legacyId);

            $createdAt = LegacyValueConverter::stampToTimestamp(
                $stampRegister > 0 ? $stampRegister : time()
            );
            $updatedAt = LegacyValueConverter::stampToTimestamp(
                $stampUpdate > 0 ? $stampUpdate : time()
            );

            if ($this->dryRun) {
                ++$inserted;
                $notes[] = sprintf('DRY: user#%d -> %s (%s)', $legacyId, $legacyLogin, $legacyMail);
                continue;
            }

            $insert->bindValue(':id', $binaryUlid, PDO::PARAM_LOB);
            $insert->bindValue(':login', $legacyLogin);
            $insert->bindValue(':name', $legacyName !== '' ? $legacyName : $legacyLogin);
            $insert->bindValue(':email', $legacyMail);
            $insert->bindValue(':pwh', $hash);
            $insert->bindValue(':active', $flagLock === 0, PDO::PARAM_BOOL);
            $insert->bindValue(':ca', $createdAt);
            $insert->bindValue(':ua', $updatedAt);
            $insert->execute();
            ++$inserted;
        }

        return new ImportReport('users', $read, $inserted, $skipped, $notes);
    }
}
