<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\Import\LegacyImport;

use PDO;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/**
 * Seed `account_title_fs_mappings` from the imported account titles.
 *
 * Legacy `accountingFSJpn.jsonJgaapFSBS / ...FSPL` hold a nested tree that
 * ties camelCase codes to FS sections. Rather than parse and reproduce
 * that tree we map via the {@see AccountTitleClassifier} category +
 * side — that is enough to place every imported title in the right
 * BS / PL bucket and lets the FS rendering endpoint aggregate totals.
 *
 * Mapping rules:
 *   asset    => bs, section=current_asset (we leave deeper splits to a
 *                   later pass; every legacy asset used is either cash /
 *                   receivable / security — all "current"-like)
 *   liability=> bs, section=current_liability
 *   equity   => bs, section=retained_earnings
 *   revenue  => pl, operating_revenue  (miscellaneous* -> non_operating_revenue)
 *   expense  => pl, sga                (tax / corporate_tax -> income_tax,
 *                                        interest-paid -> non_operating_expense)
 */
final class LegacyFsMappingImporter
{
    public function __construct(
        private readonly PDO $target,
        private readonly IdMapping $idMap,
        private readonly UlidGenerator $ulids,
        private readonly bool $dryRun,
    ) {
    }

    public function run(): ImportReport
    {
        $read = 0;
        $inserted = 0;
        $skipped = 0;
        /** @var list<string> $notes */
        $notes = [];

        $insert = $this->target->prepare(
            'INSERT INTO account_title_fs_mappings
                 (id, entity_id, account_title_id, fs_kind, fs_section_code,
                  sort_order, display_label, sign)
             VALUES
                 (:id, :ent, :at, :kind, :sec, :sort, NULL, 1)'
        );

        // Iterate every known account_title mapping and generate one
        // FS-mapping row per (entity, title). Using `allFor()` keeps the
        // dry-run code path working because no DB row exists during
        // dry-run but the in-memory cache still holds the mappings from
        // the earlier `account-titles` stage.
        $mappings = $this->idMap->allFor(IdMapping::TABLE_ACCOUNT_TITLES);
        if ($mappings === []) {
            return ImportReport::empty('fs_mappings', ['no account_titles to map']);
        }

        $sort = 1;
        foreach ($mappings as $legacyKey => $atBin) {
            ++$read;
            // legacyKey = "<entityId>:<camelCode>"
            if (!str_contains($legacyKey, ':')) {
                ++$skipped;
                continue;
            }
            [$entityIdStr, $legacyCode] = explode(':', $legacyKey, 2);
            $entityId = (int) $entityIdStr;
            $entityBin = $this->idMap->lookup(IdMapping::TABLE_ENTITIES, $entityId);
            if ($entityBin === null) {
                ++$skipped;
                continue;
            }

            [$category, , ] = AccountTitleClassifier::classify($legacyCode);
            [$kind, $section] = $this->mapSection($category, $legacyCode);

            if ($this->dryRun) {
                ++$inserted;
                ++$sort;
                continue;
            }

            $insert->bindValue(':id', $this->ulids->binary(), PDO::PARAM_LOB);
            $insert->bindValue(':ent', $entityBin, PDO::PARAM_LOB);
            $insert->bindValue(':at', $atBin, PDO::PARAM_LOB);
            $insert->bindValue(':kind', $kind);
            $insert->bindValue(':sec', $section);
            $insert->bindValue(':sort', $sort, PDO::PARAM_INT);
            $insert->execute();
            ++$inserted;
            ++$sort;
        }

        return new ImportReport('fs_mappings', $read, $inserted, $skipped, $notes);
    }

    /**
     * @return array{0:string, 1:string}
     */
    private function mapSection(string $category, string $legacyCode): array
    {
        return match ($category) {
            'asset' => ['bs', 'current_asset'],
            'liability' => ['bs', 'current_liability'],
            'equity' => ['bs', 'retained_earnings'],
            'revenue' => str_starts_with($legacyCode, 'miscellaneous')
                || $legacyCode === 'interestAndDiscountReceived'
                ? ['pl', 'non_operating_revenue']
                : ['pl', 'operating_revenue'],
            'expense' => $legacyCode === 'corporateInhabitantAndEnterpriseTax'
                ? ['pl', 'income_tax']
                : ['pl', 'sga'],
            default => ['pl', 'sga'],
        };
    }
}
