<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\Import\LegacyImport;

use PDO;
use RuntimeException;

/**
 * Import legacy `accountingEntity` rows into new `entities`.
 *
 * Every entity attaches to exactly one owning user. Legacy did not store an
 * explicit owner relationship (authority was modelled via
 * `accountingAccountEntity`), so we pick the first migrated user — which
 * matches the 1-user dataset we are migrating.
 */
final class LegacyEntityImporter
{
    public function __construct(
        private readonly PDO $source,
        private readonly PDO $target,
        private readonly IdMapping $idMap,
        private readonly bool $dryRun,
    ) {
    }

    public function run(): ImportReport
    {
        $ownerBin = $this->pickOwnerUserUlid();

        $rows = $this->source->query(
            'SELECT id, stampRegister, stampUpdate, strTitle, strNation, strCurrency
               FROM accountingEntity
              ORDER BY id'
        );
        if ($rows === false) {
            return ImportReport::empty('entities', ['source query failed']);
        }

        $read = 0;
        $inserted = 0;
        $skipped = 0;
        /** @var list<string> $notes */
        $notes = [];

        $insert = $this->target->prepare(
            'INSERT INTO entities
               (id, owner_user_id, name, nation_code, currency_code,
                fiscal_start_mmdd, is_active, created_at, updated_at)
             VALUES
               (:id, :owner, :name, :nation, :currency,
                :mmdd, :active, :ca, :ua)'
        );

        foreach ($rows as $r) {
            ++$read;
            /** @var array<string,mixed> $r */
            $legacyId = (int) $r['id'];
            $title = trim((string) ($r['strTitle'] ?? ''));
            if ($title === '') {
                ++$skipped;
                $notes[] = sprintf('entity#%d skipped (empty name)', $legacyId);
                continue;
            }

            $nation = strtoupper(trim((string) ($r['strNation'] ?? 'JPN')));
            if (strlen($nation) !== 3) {
                $nation = 'JPN';
            }
            $currency = strtoupper(trim((string) ($r['strCurrency'] ?? 'JPY')));
            if (strlen($currency) !== 3) {
                $currency = 'JPY';
            }

            $mmdd = $this->fiscalStartMmdd($legacyId);

            $binaryUlid = $this->idMap->getOrCreate(IdMapping::TABLE_ENTITIES, $legacyId);

            $createdAt = LegacyValueConverter::stampToTimestamp((int) $r['stampRegister']);
            $updatedAt = LegacyValueConverter::stampToTimestamp((int) $r['stampUpdate']);

            if ($this->dryRun) {
                ++$inserted;
                $notes[] = sprintf('DRY: entity#%d -> %s (%s/%s)', $legacyId, $title, $nation, $currency);
                continue;
            }

            $insert->bindValue(':id', $binaryUlid, PDO::PARAM_LOB);
            $insert->bindValue(':owner', $ownerBin, PDO::PARAM_LOB);
            $insert->bindValue(':name', $title);
            $insert->bindValue(':nation', $nation);
            $insert->bindValue(':currency', $currency);
            $insert->bindValue(':mmdd', $mmdd);
            $insert->bindValue(':active', true, PDO::PARAM_BOOL);
            $insert->bindValue(':ca', $createdAt);
            $insert->bindValue(':ua', $updatedAt);
            $insert->execute();
            ++$inserted;
        }

        return new ImportReport('entities', $read, $inserted, $skipped, $notes);
    }

    private function pickOwnerUserUlid(): string
    {
        $stmt = $this->source->query('SELECT id FROM baseAccount ORDER BY id LIMIT 1');
        if ($stmt === false) {
            throw new RuntimeException('pickOwnerUserUlid: cannot read baseAccount');
        }
        /** @var array<string,mixed>|false $row */
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row === false) {
            throw new RuntimeException('pickOwnerUserUlid: no user in baseAccount');
        }
        return $this->idMap->require(IdMapping::TABLE_USERS, (int) $row['id']);
    }

    /**
     * Read fiscal start month (MMDD) from accountingEntityJpn.
     * Uses the earliest period's month.
     */
    private function fiscalStartMmdd(int $entityId): string
    {
        $stmt = $this->source->prepare(
            'SELECT numFiscalBeginningMonth
               FROM accountingEntityJpn
              WHERE idEntity = :e
              ORDER BY numFiscalPeriod ASC
              LIMIT 1'
        );
        $stmt->execute([':e' => $entityId]);
        /** @var string|false $month */
        $month = $stmt->fetchColumn();
        if ($month === false) {
            return '0101';
        }
        $m = (int) $month;
        if ($m < 1 || $m > 12) {
            return '0101';
        }
        return sprintf('%02d01', $m);
    }
}
