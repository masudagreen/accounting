<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\TrialBalance;

use DateTimeImmutable;
use DateTimeZone;
use PDO;
use Rucaro\Application\TrialBalance\QueryTrialBalanceUseCase;
use Rucaro\Application\TrialBalance\QueryTrialBalanceUseCaseInput;
use Rucaro\Http\Middleware\AuthenticateBearer;
use Rucaro\Http\Response\EnvelopeResponse;
use Rucaro\Http\Response\ErrorResponse;
use Rucaro\Http\Response\JsonResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/**
 * GET /api/v1/trial-balance
 *
 * Query params:
 *   - entityId      (required, ULID)
 *   - fiscalTermId  (required, ULID)
 *   - asOf          (optional, YYYY-MM-DD; defaults to today)
 *   - from          (optional, YYYY-MM-DD; defaults to fiscal term start date)
 *   - format        (optional, json|csv; default json)
 *
 * Responses:
 *   - JSON: envelope{ data: TrialBalance }
 *   - CSV:  `text/csv` with UTF-8 BOM
 */
final readonly class GetTrialBalanceController
{
    public function __construct(
        private QueryTrialBalanceUseCase $useCase,
        private AuthenticateBearer $auth,
        private PDO $pdo,
    ) {
    }

    public function __invoke(ServerRequest $request): JsonResponse
    {
        $userId = $this->auth->authenticate($request->header('authorization'));
        if ($userId === null) {
            return ErrorResponse::unauthorized();
        }

        $entityId = $request->queryString('entityId');
        $fiscalTermId = $request->queryString('fiscalTermId');
        if ($entityId === null || $fiscalTermId === null) {
            return ErrorResponse::badRequest('entityId and fiscalTermId query parameters are required.');
        }
        if (!UlidGenerator::isValid($entityId) || !UlidGenerator::isValid($fiscalTermId)) {
            return ErrorResponse::badRequest('entityId / fiscalTermId must be valid ULIDs.');
        }

        $asOf = self::parseDate($request->queryString('asOf'))
            ?? new DateTimeImmutable('now', new DateTimeZone('UTC'));

        $from = self::parseDate($request->queryString('from'))
            ?? $this->lookupFiscalTermStart($fiscalTermId)
            ?? $asOf;

        $format = $request->queryString('format') ?? 'json';

        $tb = $this->useCase->execute(new QueryTrialBalanceUseCaseInput(
            entityId: $entityId,
            fiscalTermId: $fiscalTermId,
            fiscalTermStartDate: $from,
            asOf: $asOf,
        ));

        if ($format === 'csv') {
            $csv = TrialBalanceSerializer::toCsv($tb);
            return new JsonResponse(
                status: 200,
                headers: [
                    'Content-Type'        => 'text/csv; charset=utf-8',
                    'Content-Disposition' => 'attachment; filename="trial-balance.csv"',
                ],
                body: $csv,
            );
        }

        return EnvelopeResponse::ok(TrialBalanceSerializer::toArray($tb));
    }

    private static function parseDate(?string $raw): ?DateTimeImmutable
    {
        if ($raw === null || $raw === '') {
            return null;
        }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $raw)) {
            return null;
        }
        try {
            return new DateTimeImmutable($raw, new DateTimeZone('UTC'));
        } catch (\Exception) {
            return null;
        }
    }

    private function lookupFiscalTermStart(string $fiscalTermId): ?DateTimeImmutable
    {
        $stmt = $this->pdo->prepare('SELECT start_date FROM fiscal_terms WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => UlidGenerator::decode($fiscalTermId)]);
        /** @var string|false $raw */
        $raw = $stmt->fetchColumn();
        if ($raw === false || !is_string($raw) || $raw === '') {
            return null;
        }
        try {
            return new DateTimeImmutable($raw, new DateTimeZone('UTC'));
        } catch (\Exception) {
            return null;
        }
    }
}
