<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Ui\Planning;

use DateTimeImmutable;
use DateTimeZone;
use PDO;
use Rucaro\Application\AccountTitle\ListAccountTitlesUseCase;
use Rucaro\Application\AccountTitle\ListAccountTitlesUseCaseInput;
use Rucaro\Domain\AccountTitle\AccountTitle;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/**
 * Shared UI helpers for the Phase 7-4-C planning / management CRUD pages.
 *
 * Centralises the lookups that every planning screen needs (account titles,
 * fiscal terms, layout boilerplate) so each controller stays focused on its
 * specific verbs and does not reach into PDO directly.
 */
final readonly class PlanningUiContext
{
    public function __construct(
        private ListAccountTitlesUseCase $listAccountTitles,
        private PDO $pdo,
    ) {
    }

    /**
     * @return list<array{id: string, code: string, name: string, category: string, normalSide: string}>
     */
    public function accountTitlesForEntity(string $entityId): array
    {
        $out = $this->listAccountTitles->execute(new ListAccountTitlesUseCaseInput(
            entityId: $entityId,
            page: 1,
            pageSize: 500,
            category: null,
            isActive: true,
            search: null,
        ));
        return array_map(
            static fn (AccountTitle $a): array => [
                'id'         => $a->id,
                'code'       => $a->code,
                'name'       => $a->name,
                'category'   => $a->category,
                'normalSide' => $a->normalSide,
            ],
            $out->items,
        );
    }

    /**
     * Load fiscal terms for an entity, newest period first.
     *
     * @return list<array{id: string, fiscalPeriod: int, startDate: string, endDate: string}>
     */
    public function fiscalTermsForEntity(string $entityId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, fiscal_period, start_date, end_date
               FROM fiscal_terms
              WHERE entity_id = :entity
              ORDER BY fiscal_period DESC',
        );
        $stmt->execute([':entity' => UlidGenerator::decode($entityId)]);
        /** @var list<array<string, mixed>> $rows */
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $out = [];
        foreach ($rows as $r) {
            $idRaw = $r['id'] ?? null;
            if (!is_string($idRaw) || $idRaw === '') {
                continue;
            }
            $out[] = [
                'id'           => strlen($idRaw) === 16 ? UlidGenerator::encode($idRaw) : $idRaw,
                'fiscalPeriod' => (int) ($r['fiscal_period'] ?? 0),
                'startDate'    => (string) ($r['start_date'] ?? ''),
                'endDate'      => (string) ($r['end_date'] ?? ''),
            ];
        }
        return $out;
    }

    /**
     * Look up (start, end) for a single fiscal term.
     *
     * @return array{id: string, startDate: string, endDate: string}|null
     */
    public function findFiscalTerm(string $fiscalTermId): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, start_date, end_date
               FROM fiscal_terms
              WHERE id = :id
              LIMIT 1',
        );
        $stmt->execute([':id' => UlidGenerator::decode($fiscalTermId)]);
        /** @var array<string, mixed>|false $row */
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row === false) {
            return null;
        }
        $idRaw = $row['id'] ?? '';
        if (!is_string($idRaw) || $idRaw === '') {
            return null;
        }
        return [
            'id'        => strlen($idRaw) === 16 ? UlidGenerator::encode($idRaw) : $idRaw,
            'startDate' => (string) ($row['start_date'] ?? ''),
            'endDate'   => (string) ($row['end_date'] ?? ''),
        ];
    }

    /**
     * Pick a sensible default fiscal term when none is selected: prefer the
     * term that contains `now`, otherwise the most recent.
     *
     * @param list<array{id: string, fiscalPeriod: int, startDate: string, endDate: string}> $terms
     */
    public static function defaultFiscalTermId(array $terms, DateTimeImmutable $now): ?string
    {
        if ($terms === []) {
            return null;
        }
        $today = $now->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d');
        foreach ($terms as $t) {
            if ($t['startDate'] !== '' && $t['endDate'] !== ''
                && $today >= $t['startDate'] && $today <= $t['endDate']
            ) {
                return $t['id'];
            }
        }
        return $terms[0]['id'];
    }
}
