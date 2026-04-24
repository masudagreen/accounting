<?php

declare(strict_types=1);

namespace Rucaro\Application\FinancialStatement\Multi;

use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;
use Rucaro\Application\FinancialStatement\GenerateFinancialStatementUseCaseInput;
use Rucaro\Domain\FinancialStatement\Multi\MultiPeriodEntry;
use Rucaro\Domain\FinancialStatement\Multi\MultiPeriodFinancialStatement;
use Rucaro\Support\Clock\ClockInterface;
use Rucaro\Support\Clock\SystemClock;

/**
 * Port of the legacy `FinancialStatementMulti` calculation loop onto the
 * layered architecture.
 *
 * Responsibilities:
 *   1. Validate input (1〜{@see GenerateMultiPeriodFinancialStatementInput::MAX_PERIODS}
 *      term ids, all distinct).
 *   2. Resolve each term's metadata (date range + "第 N 期" label).
 *   3. Sort terms ascending by `start_date` so the rightmost column is the
 *      latest period — matching how every Japanese multi-period FS is printed.
 *   4. Delegate the real FS computation to the injected
 *      {@see FinancialStatementProviderInterface} (production: the Phase 6-A
 *      Port use case) once per period.
 *   5. Bundle the results into a {@see MultiPeriodFinancialStatement}
 *      aggregate; the infrastructure layer takes over for comparison-row
 *      flattening and rendering.
 *
 * Explicitly out of scope:
 *   - Computing the flat `MultiPeriodSectionRow` list. The renderer builds
 *     that from the aggregate so Smarty templates can choose how to interleave
 *     subtotals / totals; keeping it out of the domain leaves the aggregate
 *     trivially serialisable for the `format=json` path.
 *   - Beginning-cash carry-over across periods. The single-period use case
 *     already handles beginning cash = 0 (Wave 6-B), so each column shows
 *     "current-period CS only"; a future wave can seed priorRows via the
 *     provider input once the prior-period snapshot port lands.
 */
final readonly class GenerateMultiPeriodFinancialStatementUseCase
{
    public function __construct(
        private FinancialStatementProviderInterface $provider,
        private FiscalTermMetadataRepositoryInterface $fiscalTerms,
        private ClockInterface $clock = new SystemClock(),
    ) {
    }

    public function execute(GenerateMultiPeriodFinancialStatementInput $input): MultiPeriodFinancialStatement
    {
        $ids = self::validateIds($input->fiscalTermIds);

        $metas = $this->fiscalTerms->findByIds($ids);
        if (count($metas) !== count($ids)) {
            throw new InvalidArgumentException(
                'One or more fiscal term ids could not be resolved for the given entity.',
            );
        }

        // Sort ascending by start date — latest period rendered rightmost.
        usort(
            $metas,
            static fn (FiscalTermMetadata $a, FiscalTermMetadata $b): int
                => $a->startDate <=> $b->startDate,
        );

        $entries = [];
        foreach ($metas as $meta) {
            $asOf = $input->asOf ?? $meta->endDate;
            $fs = $this->provider->provide(new GenerateFinancialStatementUseCaseInput(
                entityId: $input->entityId,
                fiscalTermId: $meta->id,
                kind: $input->kind,
                fromDate: $meta->startDate,
                asOf: $asOf,
                currencyCode: $input->currencyCode,
            ));
            $entries[] = new MultiPeriodEntry(
                fiscalTermId: $meta->id,
                fiscalTermLabel: $meta->label,
                fromDate: $meta->startDate,
                toDate: $asOf,
                statement: $fs,
            );
        }

        $generatedAt = $this->clock->getCurrentTime()->setTimezone(new DateTimeZone('UTC'));

        return new MultiPeriodFinancialStatement(
            entityId: $input->entityId,
            kind: $input->kind,
            periods: $entries,
            generatedAt: $generatedAt,
        );
    }

    /**
     * @param list<string> $ids
     * @return list<string>
     */
    private static function validateIds(array $ids): array
    {
        if ($ids === []) {
            throw new InvalidArgumentException('fiscalTermIds must contain at least one id.');
        }
        if (count($ids) > GenerateMultiPeriodFinancialStatementInput::MAX_PERIODS) {
            throw new InvalidArgumentException(sprintf(
                'fiscalTermIds must not exceed %d entries (got %d).',
                GenerateMultiPeriodFinancialStatementInput::MAX_PERIODS,
                count($ids),
            ));
        }
        $seen = [];
        foreach ($ids as $id) {
            if (isset($seen[$id])) {
                throw new InvalidArgumentException('fiscalTermIds must be distinct.');
            }
            $seen[$id] = true;
        }
        return $ids;
    }
}
